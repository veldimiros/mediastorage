<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Message\AMQPMessage;

class QueueCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('queue')
                ->setDescription('...')
                ->addArgument('action', InputArgument::OPTIONAL, 'Action description')
                ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $rabbit AppBundle\Service\Rabbit  */
        $rabbit = $this->getContainer()->get('rabbit');
        $action = $input->getArgument('action');

        if ($action == 'declare') {
            $rabbit->declareChannel();
        }

        if ($action == 'listen') {
            $rabbit->getChannel()->basic_qos(null, 1, null); // only one task per consume
            $rabbit->getChannel()->basic_consume('email', '', false, false, false, false, [$this, 'sendEmail']);

            while (count($rabbit->getChannel()->callbacks)) {
                try {
                    $rabbit->getChannel()->wait();
                } catch (\Exception $e) {
                    dump($e->getMessage());
                    break;
                }
            }

            $rabbit->getChannel()->close();
            $rabbit->getConnection()->close();
        }
    }

    public function sendEmail(AMQPMessage $message)
    {
        $body = $message->body;
        $content = json_decode($body, true);
        dump('Got message: ', $body);

        /** @var AMQPChannel $channel */
        $channel = $message->get('channel');
        $delivery_tag = $message->get('delivery_tag');

        try {
            // sending email with SwiftMessage
            $message = \Swift_Message::newInstance()
                    ->setSubject('Hello from mediastorage.app!')
                    ->setFrom('ms.app@example.com')
                    ->setTo($content['email'])
                    ->setBody(
                    $this->getContainer()->get('templating')->render(
                            'AppBundle:File:email.html.twig', array('link' => $content['link'])
                    ), 'text/html'
                    )
            ;
            $result = $this->getContainer()->get('mailer')->send($message);

            switch ($result) {
                case 1:
                    //  acknowledge the receipt of a message
                    $channel->basic_ack($delivery_tag);
                    dump('Sending email succeed');
                    break;

                case 0:
                    // message is not acknowledged
                    $channel->basic_nack($delivery_tag, false, false);
                    dump('Sending emai failed');
                    break;

                default:
                    // new try
                    $channel->basic_ack($delivery_tag);
            }
        } catch (\Exception $e) {
            dump('FAILED. ' . $e->getMessage());
        }
    }

}
