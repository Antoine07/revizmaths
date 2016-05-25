<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 16/05/2016
 * Time: 23:57
 */

namespace Reviz\FrontBundle\Utils;


class AntiSpam
{

    private $word;
    private $parameters;

    public function __construct(Regex $regex, $foo, $bar)
    {
        $this->word = $regex->get('hello');
        $this->parameters[] = $foo;
        $this->parameters[] = $bar;
    }

    public function isSpam($text)
    {
        return preg_match("/{$this->word}/", $text);
    }
}