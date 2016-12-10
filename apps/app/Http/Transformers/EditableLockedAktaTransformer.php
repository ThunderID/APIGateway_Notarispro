<?php

namespace App\Http\Transformers;

class EditableLockedAktaTransformer
{
	public function transform(array $akta, array $lock)
	{
		$major['page_info']			= ['id' => $akta['id']];

		$minor['title']['header']	= ['title'];
		$minor['title']['data']		= 	[
											'title'		=> 
												[
													'element-class'			=> 'input',
													'element-type'			=> 'string',
													'element-properties'	=> 
															[
	    														'value'			=> $value['title'],
	    														'validation' 	=> 
	    															[
	    																'required'	=> true,
	    																'max'		=> 255,
	    															],
															],
												]
										];

		foreach ($akta['paragraph'] as $key => $value) 
		{
			$visible			= true;
			foreach ($lock['field'] as $key2 => $value2) 
			{
				if(str_is($value2, 'paragraph.'.$key.'content'))
				{
					$visible	= false;
				}
			}

			$minor['content']['header'][$key]	= 'paragraph_'.$key;
			$minor['content']['data'][$key]		= [
													'paragraph_'.$key 	=>
														[
															'element-class'			=> 'input',
															'element-type'			=> 'string',
															'element-properties'	=> 
																	[
			    														'value'			=> $value['title'],
			    														'validation' 	=> 
			    															[
			    																'required'	=> true,
			    																'max'		=> 255,
			    																'visible'	=> $visible,
			    															],
																	],
														]
												  ];
		}

		$major['page_info']	= $minor;

		return $major;
	}
}