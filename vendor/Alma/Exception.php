<?php
namespace Alma;

class Exception extends \Exception
{
    private $m_title;

    public function __construct($message, $title = 'フレームワークエラー', $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->m_title = $title;
    }

    public function getTitle()
    {
        return $this->m_title;
    }
}