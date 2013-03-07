<?php
namespace Esendex\Model;

class ResultItem
{
    private $id;
    private $uri;

    /**
     * @param $id
     * @param $uri
     */
    public function __construct($id, $uri)
    {
        $this->id = (string)$id;
        $this->uri = (string)$uri;
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function uri()
    {
        return $this->uri;
    }
}
