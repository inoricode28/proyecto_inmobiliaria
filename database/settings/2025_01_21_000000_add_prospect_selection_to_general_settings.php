<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddProspectSelectionToGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.enable_prospect_selection_in_proforma', false);
    }
}