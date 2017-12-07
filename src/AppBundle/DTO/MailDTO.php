<?php
/**
 * Created by PhpStorm.
 * User: navrotskiy
 * Date: 07.12.17
 * Time: 10:36
 */

namespace AppBundle\DTO;


class MailDTO
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var array
     */
    private $bodyData;

    /**
     * @var string
     */
    private $view;

    /**
     * @var string
     */
    private $type;

    /**
     * MailDTO constructor.
     * @param string $subject
     * @param string $from
     * @param string $to
     * @param array $bodyData
     * @param string $view
     * @param string $type
     */
    public function __construct(
        $subject = '',
        $from = '',
        $to = '',
        array $bodyData = [],
        $view = '',
        $type = 'text/html'
    ) {
        $this->subject = $subject;
        $this->from = $from;
        $this->to = $to;
        $this->bodyData = $bodyData;
        $this->view = $view;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return array
     */
    public function getBodyData()
    {
        return $this->bodyData;
    }

    /**
     * @param array $bodyData
     */
    public function setBodyData(array $bodyData)
    {
        $this->bodyData = $bodyData;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


}