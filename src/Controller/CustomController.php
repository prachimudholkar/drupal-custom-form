<?php

namespace Drupal\formmie\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Messenger;
use Drupal\Core\Link;

class CustomController extends ControllerBase 
{

	public function content() 
	{
		//Creating the header of the table
		$myheader = [
		'id'=>$this->t('ID'), 
		'first_name'=>$this->t('Name'), 
		'last_name'=>$this->t('Surname'), 
		'email'=>$this->t('Email'), 
		'phone'=>$this->t('Phone'), 
		'dob'=>$this->t('Date of Birth'), 
		'opt'=>$this->t('Edit Operation'), 
		'opt1'=>$this->t('Delete Operation'),
		];
		
		$row = [];
		
		//conecting to the database
		$database = \Drupal::database();
		
		//fetching records from the database "formtable"
		$query = $database->query("SELECT * FROM formtable");
		$result = $query->fetchAll();
			
			
		foreach($result as $value)
		{
			//getting id for deleting specific record
			$delete = Url::fromUserInput('/formtable/form/delete/'.$value->id);
			
			//getting id for updating specific record
			$edit = Url::fromUserInput('/formtable/form?id='.$value->id);
			
			//listing records in the table
			$row[] = [
			'id'=>$value->id, 
			'first_name'=>$value->first_name, 
			'last_name'=>$value->last_name, 
			'phone'=>$value->phone,
			'email'=>$value->email, 
			'dob'=>$value->dob, 
			'opt'=>Link::fromTextAndUrl('Edit',$edit)->toString(), 
			'opt1'=>Link::fromTextAndUrl('Delete',$delete)->toString(),
			];
		}
		
		//adding new record in the database
		$add = Url::fromUserInput('/formtable/form');
		
		//creating add button
		$text = [
		'#type'=>'button',
		'#value'=>t('Add User'),
		];
		
		//creatin the table
		$data['table'] = 
		[
			'#type'=>'table',
			'#header'=>$myheader,
			'#rows'=>$row,
			'#empty'=>t('No record found'),
			'#caption'=>Link::fromTextAndUrl($text,$add)->toString(),
		];			
		
		//a message for validating the action taken
		$this->messenger()->addMessage('Records Listed');
		
		return $data;
	}
}

?>