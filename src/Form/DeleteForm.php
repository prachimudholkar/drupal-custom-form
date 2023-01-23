<?php

namespace Drupal\formmie\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Messenger;
use Drupal\Core\Url;

//ConfirmFormBase is used to confirm an action on the form e.g. delete
class DeleteForm extends ConfirmFormBase {
	
	public function getFormName()
	{
		return 'delete_form';
	}
	
	public function getFormId()
	{
		return 'delete_form';
	}
	
	public $cid = NULL;
	
	public function getQuestion()
	{
		return t('Delete Form Record ?');
	}
	
	public function getCancelUrl()
	{
		return new Url('formmie.custom_controller');
	}
	
	public function getDescription() 
	{
		return t('Are you sure you want to delete the record ? This action cannot be undone.');
	}
	
	public function getConfirmText() 
	{
		return t('Confirm');
	}
  
	public function getCancelText() 
	{
		return t('Cancel');
	}
	
	public function buildForm(array $form, FormStateInterface $form_state, $cid = NULL) 
	{
		$this->id = $cid;
		return parent::buildForm($form, $form_state);
    }
	
	public function validateForm(array &$form, FormStateInterface $form_state) 
	{
		parent::validateForm($form, $form_state);
    }
	
	public function submitForm(array &$form, FormStateInterface $form_state)
	{
		//connecting to the database
		$query = \Drupal::database();

		//deleting records for a specific id mentioned
		$query->delete('formtable')->condition('id',$this->id)->execute();
		
		//a message for validating the action taken
		$this->messenger()->addMessage('Successfully deleted the records.');
		
		//redirecting to the list of users where we can see the record is deleted
		$form_state->setRedirect('formmie.custom_controller');
	}
	
}

?>