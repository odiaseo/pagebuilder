<?php

namespace PageBuilder;

interface PageAwareInterface
{
    /**
     * @param PageMetaData $meta
     *
     * @return mixed
     */
    public function setPageMetadata(PageMetaData $meta);

    /**
     * @return PageMetaData
     */
    public function getPageMeta();
}