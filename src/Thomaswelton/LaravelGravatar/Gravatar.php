<?php namespace Thomaswelton\LaravelGravatar;

use Config;
use Illuminate\Html\HtmlFacade as Html;

class Gravatar extends \thomaswelton\GravatarLib\Gravatar
{
    private $default_size = null;

    public function __construct()
    {
        // Enable secure images by default

        $this->setDefaultImage(Config::get('laravel-gravatar::default'));
        $this->default_size = Config::get('laravel-gravatar::size');

        $this->setMaxRating(Config::get('laravel-gravatar::maxRating', 'g'));
        $this->enableSecureImages();
    }

    public function src($email, $size = null, $rating = null)
    {
        if (is_null($size)) {
            $size = $this->default_size;
        }

        $size = max(1, min(512, $size));

        $this->setAvatarSize($size);

        if(!is_null($rating)) $this->setMaxRating($rating);

        return htmlentities($this->buildGravatarURL($email));
    }

    public function image($email, $alt = null, $attributes = array(), $rating = null)
    {
        $dimensions = array();

        if(array_key_exists('width', $attributes)) $dimensions[] = $attributes['width'];
        if(array_key_exists('height', $attributes)) $dimensions[] = $attributes['height'];

        $max_dimension = (count($dimensions)) ? min(512, max($dimensions)) : $this->default_size;

        $src = $this->src($email, $max_dimension, $rating);

        if (!array_key_exists('width', $attributes) && !array_key_exists('height', $attributes)) {
            $attributes['width'] = $this->size;
            $attributes['height'] = $this->size;
        }

        return HTML::image($src, $alt, $attributes);
    }

    public function exists($email)
	{
		$this->setDefaultImage('404');

		$url = $this->buildGravatarURL($email);
		$headers = get_headers($url, 1);

		return strpos($headers[0], '200') ? true : false;
	}
}
