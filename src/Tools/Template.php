<?php

namespace Apirone\Invoice\Tools;

use Apirone\Invoice\Invoice;

class Template
{
    public Invoice $invoice;
    
    public ?string $template = null;

    private function __construct()
    {
    }

    public static function init($invoice, $template = null)
    {
    
    }

    public function render()
    {
    }
}
