<?php
namespace PageBuilder\View;

class TagAttributes
{
    public $wrapper = 'div';
    public $class = array();
    public $id = '';
    public $container = false;
    public $container2 = false;
    public $attributes = array();
    public $options = array();

    public function __construct($options = array())
    {
        foreach ($options as $key => $val) {
            $method = 'set' . ucfirst($key);
            $val    = is_string($val) ? strip_tags($val) : $val;

            if (method_exists($this, $method)) {
                $this->$method($val);
            }
        }
    }

    protected function setAttr($attributes)
    {
        $this->attributes = $this->cleanAttr($attributes);

        return $this;
    }

    private function cleanAttr($attributes)
    {
        $attr  = array();
        $parts = explode(',', $attributes);
        $parts = array_unique(array_filter($parts));

        foreach ($parts as $item) {
            list($k, $v) = explode('=', $item);
            $k = preg_replace('/[^a-z0-9\-\_]/', '', $k);
            if ($k and $v) {

                $attr[$k] = trim($v, '"\'');
            }
        }

        return $attr;

    }

    public function formatAttr()
    {
        $attr = array();
        foreach ($this->attributes as $k => $v) {
            $attr[] = $k . '="' . $v . '"';
        }

        return implode(' ', $attr);
    }

    public function addAttr($attr)
    {
        $this->attributes = array_merge($this->attributes, $this->cleanAttr($attr));

        return $this;
    }

    public function getAttr()
    {
        return $this->attributes;
    }

    protected function setClass($class)
    {
        $class = array_unique(array_filter(explode(',', $class)));
        foreach ($class as $c) {
            $this->class[] = preg_replace('/[^a-z0-9\-\_ ]/', '', $c);
        }

        return $this;
    }

    public function addMicroData($data)
    {
        $this->attributes[] = strip_tags($data);

        return $this;
    }

    public function addClass($class)
    {
        foreach ((array)$class as $c) {
            $c   = str_replace(',', ' ', $c);
            $cls = explode(' ', $c);
            foreach ($cls as $c) {
                $this->class[] = trim(preg_replace('/[^a-z0-9\-\_ ]/', '', $c));
            }
        }
        $this->class = array_unique(array_filter($this->class));

        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }

    protected function setId($id)
    {
        $this->id = preg_replace('/[^a-z0-9\-\_]/', '', $id);

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function formatId()
    {
        if ($this->id) {
            return ' id="' . $this->id . '" ';
        } else {
            return '';
        }
    }

    public function formatClass()
    {
        if ($this->class) {
            return ' class="' . implode(' ', $this->class) . '" ';
        } else {
            return '';
        }
    }

    public function setWrapper($wrapper)
    {
        $this->wrapper = preg_replace('/[^a-z]/i', '', strtolower($wrapper));

        return $this;
    }

    public function getWrapper()
    {
        return $this->wrapper;
    }

    public function setContainer($hasContainer)
    {
        $this->container = trim(str_replace(',', ' ', $hasContainer));

        return $this;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setContainer2($container2)
    {
        $this->container2 = trim(str_replace(',', ' ', $container2));
        ;

        return $this;
    }

    public function getContainer2()
    {
        return $this->container2;
    }

}