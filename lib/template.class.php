<?php


/**
 * own wrapper around the Mustache base class + l10n
 */
class Template {

    protected $mustache = null;
    protected $l10n;

    public static function get($file) {
        return new static(file_get_contents(__DIR__."/../static/tpl/$file"));
    }

    public function __construct($template) {
        $partials = new Mustache_Loader_FilesystemLoader(dirname(__FILE__)."/../static/tpl");
        $this->mustache = new Mustache($template, null, $partials, null);
        $this->l10n = L10n::get('mustache');
    }

    public function render($view = array()) {
        $view['_'] = array($this, '_translate');
        return $this->mustache->render(null, $view, null);
    }

    public function _translate($s) {
        return $this->l10n->gettext($s);
    }

}


//__END__
