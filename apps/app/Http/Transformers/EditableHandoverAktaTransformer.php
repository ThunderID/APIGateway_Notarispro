<?php

namespace App\Http\Transformers;

class EditableHandoverAktaTransformer
{
	public function transform(array $akta, array $user)
	{
		$lists 							= [];
		$major['page_info']				= ['id' => $akta['_id']];

		foreach ($user as $key => $value) 
		{
			$lists[$key] 				=  ['option' => $value['_id'], 'value' => $value['name']];
		}

		$minor['content']['header'][0]	= 'writerid';
		$minor['content']['data'][0]	= [
											'writerid'	=>
												[
													'element-class'			=> 'input',
													'element-type'			=> 'select',
													'element-properties'	=> 
															[
	    														'value'			=> null,
	    														'validation' 	=> 
	    															[
	    																'required'	=> true,
	    																'max'		=> 255,
	    																'lists'		=> $lists,
	    															],
															],
												]
										  ];

		$major['page_data']	= $minor;

		return $major;
	}
}