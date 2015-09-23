<?php

namespace BVM\SonataAdminTestsGeneratorBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\Generator;

class SonataAdminTestsGenerator extends Generator
{
	/**
	 * @param  object $admin
	 * @return false | int
	 */
	public function generate($admin, $bundle)
	{

		$admin_name = $this->getAdminNameFromAdminCode($admin->getCode());

		$file_path = sprintf('%s/Tests/%sAdminTest.php',$bundle->getPath(), $admin_name);

		$data = $this->getDataFromAdmin($admin, $admin_name);

		return $this->renderFile('AdminTests.php.twig', $file_path, array(
			'data' =>$data,
			'namespace' => $namespace
		));
	}

	protected function getDatafromAdmin($admin, $admin_name)
	{
		$data = array();

		$data['name'] 	= $admin_name;
		$data['crud'] 	= array();

		foreach($admin->getRoutes()->getElements() as $name => $route)
		{
			$chunks = explode('.', $name);

			$route_name = array_pop($chunks);

			switch ($route_name) {

				case 'create':
						
					$fields = array();

					foreach($admin->getFormFieldDescriptions() as $key => $field)
					{
						$fields[] = array(
							'name' => $field->getName(),
							'mapping' => $field->getFieldMapping()
						);
					}

					$data['crud'][$route_name]['fields'] 	= $fields;
					$data['crud'][$route_name]['route_url'] = $admin->generateUrl('create', array('uniqid'=> 'test'));

					break;
				case 'edit':
					$url = $admin->generateUrl('edit', array('id'=> 1, 'uniqid'=> 'test'));
					$url = str_replace('/1/', '/%s/', $url);

					$data['crud'][$route_name]['route_url'] = $url;
					break;
				case 'list':
					$data['crud'][$route_name]['route_url'] = $admin->generateUrl('list');
					break;
				case 'show':
					$url = $admin->generateUrl('show', array('id'=> 1));
					$url = str_replace('/1/', '/%s/', $url);
					
					$data['crud'][$route_name]['route_url'] = $url;
					break;
				case 'delete':
					$url = $admin->generateUrl('delete', array('id'=> 1));
					$url = str_replace('/1/', '/%s/', $url);
					
					$data['crud'][$route_name]['route_url'] = $url;
					break;	
				// case 'batch':
					# code...
					// break;
				// case 'export':
					# code...
					// break;
				default:
					# code...
					break;
			}
		}

		// dump($data);
		// die();
		return $data;

	}


	/**
	 * @param  string
	 * @return string
	 */
	protected function getAdminNameFromAdminCode($admin_code)
	{
			$first = explode('.', $admin_code)[0];

			if(false !== strrpos($first, '_'))
			{
				$chunks =  explode('_', $first);

				$chunks[0] = strtoupper($chunks[0]);
				$chunks[1] = ucfirst($chunks[1]);	

				return implode('', $chunks);
			
			}

			return strtoupper($first);

	}


}