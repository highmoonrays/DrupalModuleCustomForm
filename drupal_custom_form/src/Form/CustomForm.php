<?php

declare(strict_types=1);

namespace Drupal\drupal_custom_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Class CustomForm
 * @package Drupal\drupal_custom_form\Form
 */
class CustomForm extends FormBase
{
  /**
   * @inheritDoc
   *
   * @return string
   */
  public function getFormId(): string
  {
    return 'drupal_custom_form';
  }

  /**
   * @inheritDoc
   *
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state): array
  {
    $form['firstName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
    ];

    $form['lastName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
    ];

    $form['subject'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Subject'),
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit and Mail')
    ];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $email = $form_state->getValue('email');

    $isValidEmail = filter_var($email, FILTER_VALIDATE_EMAIL);

    if ($isValidEmail === false){
      $form_state->setErrorByName('emailError', $this->t('Email is invalid!'));
    }
  }

  /**
   * @inheritDoc
   * @throws Exception
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $mail = new PHPMailer(true);

    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'omirom.omirom@gmail.com'; // enter your email here
    $mail->Password   = 'mamayapoel'; // and here the password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;


    $mail->setFrom('omirom.omirom@gmail.com', 'optional name');

    $mail->addAddress($form_state->getValue('email'));

    $mail->isHTML(false);
    $mail->Subject = $form_state->getValue('subject');
    $mail->Body    = $form_state->getValue('message');

    if ($mail->send() === false){
      $form_state->setErrorByName('sendEmailError', $this->t('By some reason email was not sent :('));
//    Logs an error
      \Drupal::logger('drupal_custom_form')->error(
        'Error occurred while sending an email to .' .
        $form_state->getValue('email')
      );
    } else {
      \Drupal::messenger()->addMessage('Form is submitted and email is sent');
      \Drupal::logger('drupal_custom_form')->info(
        'Email has been sent to '
        .$form_state->getValue('email')
      );
    }
  }
}

