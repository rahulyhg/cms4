<?php

namespace Botble\SeoHelper\Contracts\Helpers;

use Botble\SeoHelper\Contracts\RenderableContract;

interface MetaContract extends RenderableContract
{

    /**
     * Get the meta name.
     *
     * @return string
     * @author ARCANEDEV <arcanedev.maroc@gmail.com>
     */
    public function key();

    /**
     * Set the meta prefix name.
     *
     * @param  string $prefix
     *
     * @return self
     * @author ARCANEDEV <arcanedev.maroc@gmail.com>
     */
    public function setPrefix($prefix);

    /**
     * Set the meta property name.
     *
     * @param  string $nameProperty
     *
     * @return self
     * @author ARCANEDEV <arcanedev.maroc@gmail.com>
     */
    public function setNameProperty($nameProperty);

    /**
     * Make Meta instance.
     *
     * @param  string $name
     * @param  string $content
     * @param  string $propertyName
     * @param  string $prefix
     *
     * @return self
     */
    public static function make($name, $content, $propertyName = 'name', $prefix = '');

    /**
     * Check if meta is valid.
     *
     * @return bool
     * @author ARCANEDEV <arcanedev.maroc@gmail.com>
     */
    public function isValid();
}