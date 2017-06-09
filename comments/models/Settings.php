<?php namespace Jab\Comments\Models;

use Model;

class Settings extends Model {

  public $implement = ['System.Behaviors.SettingsModel'];

  public $settingsCode = 'comments_settings';
  public $settingsFields = 'fields.yaml';
}