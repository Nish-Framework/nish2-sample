<?php


namespace Models\Services\SearchDTOs;


class UserSearchDTO extends BaseSearchDTO
{
    private $text;
    private $idList;

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdList()
    {
        return $this->idList;
    }

    /**
     * @param mixed $idList
     */
    public function setIdList($idList)
    {
        $this->idList = $idList;

        return $this;
    }


}