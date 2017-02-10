<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FileRepository")
 */
class File
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="string", length=255)
     */
    private $info;

    /**
     * @var string
     *
     * @ORM\Column(name="hash_file", type="string", length=255)
     */
    private $hashFile;

    /**
     * @var string
     *
     * @ORM\Column(name="hash_email", type="string", length=255)
     */
    private $hashEmail;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Please, select file")
     * @Assert\File(maxSize="100M")
     */
    private $file;

    /**
     * @var string
     */
    private $email;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return File
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set hashFile
     *
     * @param string $hashFile
     *
     * @return File
     */
    public function setHashFile($hashFile)
    {
        $this->hashFile = $hashFile;

        return $this;
    }

    /**
     * Get hashFile
     *
     * @return string
     */
    public function getHashFile()
    {
        return $this->hashFile;
    }

    /**
     * Set hashMail
     *
     * @param string $hashEmail
     *
     * @return File
     */
    public function setHashEmail($hashEmail)
    {
        $this->hashEmail = $hashEmail;

        return $this;
    }

    /**
     * Get hashEmail
     *
     * @return string
     */
    public function getHashEmail()
    {
        return $this->hashEmail;
    }

    function getFile()
    {
        return $this->file;
    }

    function setFile($file)
    {
        $this->file = $file;
    }

    function getEmail()
    {
        return $this->email;
    }

    function setEmail($email)
    {
        $this->email = $email;
    }

}
