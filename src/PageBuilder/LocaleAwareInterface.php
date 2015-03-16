<?php
namespace PageBuilder;

/**
 * Interface LocaleAwareInterface
 *
 * @package PageBuilder
 */
interface LocaleAwareInterface
{
    /**
     * @param $language
     *
     * @return mixed
     */
    public function setLocale($language);

    /**
     * @param $namespacae
     *
     * @return mixed
     */
    public static function setNamespace($namespacae);
}
