<?php

namespace App\Classes;

use Image;

class Images 
{

	public function setImageList($posters, $listId)
	{
		$img = Image::canvas(166, 248, '#202326');
		$image1 = Image::make(public_path() . '/assets/posters/medium' . $posters[0])->resize(83, 124);
		$image2 = Image::make(public_path() . '/assets/posters/medium' . $posters[1])->resize(83, 124);
		$image3 = Image::make(public_path() . '/assets/posters/medium' . $posters[2])->resize(83, 124);
		$image4 = Image::make(public_path() . '/assets/posters/medium' . $posters[3])->resize(83, 124);
		$img->insert($image1, 'top-left');
		$img->insert($image2, 'top-right');
		$img->insert($image3, 'bottom-left');
		$img->insert($image4, 'bottom-right');
		$img->save(public_path() . '/assets/imagelists/' . $listId . '.jpg');
	}

}
