<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    public string $site_name;
    public bool $enable_registration;
    public string|null $site_logo;
    public bool $enable_social_login;
    public string|null $site_language;
    public string|null $default_role;
    public bool $enable_login_form;
    public bool $enable_oidc_login;
     public bool $enable_prospect_selection_in_proforma; // Temporalmente comentado hasta resolver el problema

    public static function group(): string
    {
        return 'general';
    }

}
