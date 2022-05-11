<?php


namespace Models\Services\SearchDTOs;


use Configs\Config;

abstract class BaseSearchDTO
{
    protected $arrayReturnRequired = false;
    protected $currentPage = 1;
    protected $pageLimit = Config::PAGINATION_LIMIT;
    protected $cacheUsageNeeded = true;

    /**
     * @return bool
     */
    public function isArrayReturnRequired(): bool
    {
        return $this->arrayReturnRequired;
    }

    /**
     * @param bool $arrayReturnRequired
     * @return $this
     */
    public function setArrayReturnRequired(bool $arrayReturnRequired)
    {
        $this->arrayReturnRequired = $arrayReturnRequired;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param int|null $currentPage
     * @return $this
     */
    public function setCurrentPage(?int $currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageLimit(): int
    {
        return $this->pageLimit;
    }

    /**
     * @param int|null $pageLimit
     * @return $this
     */
    public function setPageLimit(?int $pageLimit)
    {
        $this->pageLimit = $pageLimit;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCacheUsageNeeded(): bool
    {
        return $this->cacheUsageNeeded;
    }

    /**
     * @param bool $cacheUsageNeeded
     * @return $this
     */
    public function setCacheUsageNeeded(bool $cacheUsageNeeded)
    {
        $this->cacheUsageNeeded = $cacheUsageNeeded;
        return $this;
    }
}