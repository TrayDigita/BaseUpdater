<?php
/*
 * Copyright (C) 2021 Tray Digita
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=1);

namespace TrayDigita\BaseUpdater\Plugin;

use function explode;
use function is_bool;
use function is_string;
use function str_replace;
use function strtolower;
use function ucwords;

/**
 * @property-read string $slug
 * @property-read string $base_slug
 * @property-read string $version
 * @property-read string $name
 * @property-read string $plugin_uri
 * @property-read string $author
 * @property-read string $author_name
 * @property-read string $author_uri
 * @property-read string $text_domain
 * @property-read string $domain_path
 * @property-read bool $network
 * @property-read string $requires_wp
 * @property-read string $requires_php
 * @property-read string $update_uri
 * @property-read string $title
 * @property-read array<string, string|bool> $data
 *
 * @method string getVersion()
 * @method string getName()
 * @method string getPluginUri()
 * @method string getAuthor()
 * @method string getAuthorName()
 * @method string getAuthorUri()
 * @method string getTextDomain()
 * @method string getDomainPath()
 * @method bool   getNetwork()
 * @method string getRequiresWP()
 * @method string getRequiresPHP()
 * @method string getUpdateURI()
 * @method string getTitle()
 */
final class PluginInfo
{
    /**
     * Default plugin info
     *
     * @var array<string, string|bool>
     */
    private $default = [
        'Name' => '',
        'PluginURI' => '',
        'Version' => '',
        'Description' => '',
        'Author' => '',
        'AuthorURI' => '',
        'TextDomain' => '',
        'DomainPath' => '',
        'Network' => false,
        'RequiresWP' => '',
        'RequiresPHP' => '',
        'UpdateURI' => '',
        'Title' => '',
        'AuthorName' => '',
    ];

    /**
     * @var string
     */
    protected $base_slug;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param string $slug
     * @param array $metadata
     */
    public function __construct(string $slug, array $metadata)
    {
        $this->slug = $slug;
        $this->base_slug = (string) (explode('/', $slug)[0]);
        $this->data = $this->normalize($metadata);
    }

    /**
     * @return bool[]|string[]
     */
    public function getDefault() : array
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getBaseSlug(): string
    {
        return $this->base_slug;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isNetwork() : bool
    {
        return (bool) $this->get('Network');
    }

    /**
     * @param array $metadata
     *
     * @return array
     */
    protected function normalize(array $metadata) : array
    {
        $meta = $this->default;
        foreach ($metadata as $key => $value) {
            if (! is_string($key)) {
                continue;
            }
            if ($key === 'Network') {
                $value = is_bool($value) ? $value : $value === 'true';
            }
            $meta[$key] = $value;
        }
        $meta['Title'] = ($meta['Title']?:$meta['Name']);
        $meta['AuthorName'] = ($meta['AuthorName']?:$meta['Author']);
        return $meta;
    }

    /**
     * @param string $name
     *
     * @return bool|mixed|string
     */
    public function get(string $name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        $lowerName = strtolower(str_replace([' ', '-'], '_', $name));
        switch ($lowerName) {
            case 'name':
            case 'title':
            case 'description':
            case 'author':
                $name = ucwords($lowerName);
                break;
            case 'network':
            case 'is_network':
            case 'isnetwork':
                $name = 'Network';
                break;
            case 'authorname':
            case 'author_name':
                $name = 'AuthorName';
                break;
            case 'require_wp':
            case 'requires_wp':
            case 'requireswp':
            case 'requirewp':
            case 'requires':
                $name = 'Requires';
                break;
            case 'require_php':
            case 'requires_php':
            case 'requiresphp':
            case 'requirephp':
                $name = 'RequiresPHP';
                break;
            case 'updateurl':
            case 'updateuri':
            case 'update_uri':
            case 'update_url':
                $name = 'UpdateURI';
                break;
            case 'pluginurl':
            case 'pluginuri':
            case 'plugin_uri':
            case 'plugin_url':
                $name = 'PluginURI';
                break;
            case 'textdomain':
            case 'text_domain':
                $name = 'TextDomain';
                break;
            case 'domainpath':
            case 'domain_path':
                $name = 'DomainPath';
                break;
            case 'plugin_version':
            case 'pluginversion':
                $name = 'Version';
                break;
            case 'slug':
                return $this->slug;
            case 'base_slug':
            case 'baseslug':
                return $this->base_slug;
        }

        return $this->data[$name]??'';
    }

    /**
     * @param string $name
     *
     * @return mixed|string|array<string, string|bool>
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'slug':
                return $this->getSlug();
            case 'data':
                return $this->getData();
            case 'base_slug':
                return $this->getBaseSlug();
        }

        return $this->get($name);
    }

    public function __set()
    {
        // pass
    }

    /**
     * Magic method call
     *
     * @param string $name
     * @param array $arguments
     *
     * @return bool|mixed|string
     */
    public function __call(string $name, array $arguments = [])
    {
        $name = preg_replace('~^(?:get[_]*)?~i', '', $name);
        return $this->get($name);
    }
}
