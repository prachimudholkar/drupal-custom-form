<?php

namespace Drupal\formmie\Form;		//to define in which space we are working in 

use Drupal\Core\Form\FormBase; 					//various interfaces and classes
use Drupal\Core\Form\FormInterface;             // which we are going to use 
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Messenger;
use Drupal\Core\Link;


//FormBase implements FormInterface
//Provides a base class for forms
class CustomForm extends FormBase            
{
	public function getFormid()
	{
		return 'custom_form';
	}
	
	public function buildform(array $form, FormStateInterface $form_state)
	{
		//connecting to the database
		$database = \Drupal::database();
		
		$record = [];
		
		//fetching data from the database, if any
		if(isset($_GET['id']))
		{	
			$theid = $_GET['id'];
			$query = $database->query("SELECT * FROM formtable where id = $theid");
			$record = $query->fetchAll();
		}
		
		//form array for creating fields
		$form['first_name'] = [
		'#type'=>'textfield',
		'#title'=>t('Name'),
		'#required'=>TRUE,
		'#default_value'=>(isset($record['first_name'])&&$_GET['id'])? $record['first_name']:'',
		];
		
		$form['last_name'] = [
		'#type'=>'textfield',
		'#title'=>t('Surname'),
		'#required'=>TRUE,
		'#default_value'=>(isset($record['last_name'])&&$_GET['id'])? $record['last_name']:'',
		];
		
		$form['phone'] = [
		'#type'=>'number',
		'#title'=>t('Phone'),
		'#required'=>TRUE,
		'#default_value'=>(isset($record['phone'])&&$_GET['id'])? $record['phone']:'',
		];
		
		$form['email'] = [
		'#type'=>'email',
		'#title'=>t('Email'),
		'#required'=>TRUE,
		'#default_value'=>(isset($record['email'])&&$_GET['id'])? $record['email']:'',
		];
		
		$form['dob'] = [
		'#type'=>'date',
		'#title'=>t('Date of Birth '),
		'#required'=>TRUE,
		'#default_value'=>(isset($record['dob'])&&$_GET['id'])? $record['dob']:'',
		];
		
		$form['action'] = [
		'#type'=>'action',
		];
		
		$form['action']['submit'] = [
		'#type' => 'submit',
		'#value' => t('Submit'),
		];
		
		$form['action']['reset'] = [
		'#type'=>'button',
		'#value'=>t('Reset'),
		'#attributes'=>['onclick'=>'this.form.reset(); return false;',],
		];
		
		return $form;
		
	}
	
	public function validateForm(array &$form, FormStateInterface $form_state) 
	{
		//validating the phone number
		if (strlen($form_state->getValue('phone')) <= 6) 
		{
			//setting error on the given condition
		  $form_state->setErrorByName('phone', $this->t('The phone number is too short. Please enter a full phone number.'));
		}
		
		//refers to the function in FormBase
		parent::validateForm($form, $form_state);
	}
	
	public function submitForm(array &$form, FormStateInterface $form_state) 
	{
		
		//getting values from the form
		$field = $form_state->getValues();
		
		//assigning form values to variables
		$first_name = $field['first_name'];
		$last_name = $field['last_name'];
		$phone = $field['phone'];
		$email = $field['email'];
		$dob = $field['dob'];
		
		//if the entries already exist for the id then it lets us update the records (updating exisitng user)
		if(isset($_GET['id']))
		{
			//adding values from the form to respective fields in the database
			$field = [
			'first_name'=> $first_name,
			'last_name'=> $last_name,
			'phone'=> $phone,
			'email'=> $email,
			'dob'=> $dob,
			];
			
			//connecting to the database
			$query = \Drupal::database();
			
			//updating values in the database for a specific id
			$query->update('formtable')->fields($field)->condition('id',$_GET['id'])->execute();
			
			//a message for validating the action taken
			$this->messenger()->addMessage("Successfully updated records");
			
			//redirecting the list of users
			$form_state->setRedirect('formmie.custom_controller');
		}
		//adding new records to the database (adding new user)
		else
		{
			//adding values from the form to respective fields in the database
			$field = [
			'first_name'=> $first_name,
			'last_name'=> $last_name,
			'phone'=> $phone,
			'email'=> $email,
			'dob'=> $dob,
			];
			
			//connecting to the database
			$query = \Drupal::database();
			
			//inserting values in the database, creating new record
			$query->insert('formtable')->fields($field)->execute();
			
			//a message for validating the action taken
			$this->messenger()->addMessage("Successfully saved new records");
			
			//redirecting the list of users
			$form_state->setRedirect('formmie.custom_controller');
		}
	}	
}
?>
 

