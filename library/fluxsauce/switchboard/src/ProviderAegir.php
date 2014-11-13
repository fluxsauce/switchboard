<?php
/**
 * @file
 * Acquia specific API interactions.
 */

namespace Fluxsauce\Switchboard;

class ProviderAegir extends Provider {
  protected $name = 'aegir';
  protected $label = 'Aegir';
  protected $homepage = 'http://www.aegirproject.org/';
  
  // This API doesn't exist, and it would be different per aegir host.  We need to figure this out.
  protected $endpoint = 'https://api.aegirproject.org/v1';
}
