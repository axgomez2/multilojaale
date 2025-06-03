<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Título da página
     *
     * @var string
     */
    public $title;

    /**
     * Descrição da página para SEO
     *
     * @var string
     */
    public $description;

    /**
     * Palavras-chave para SEO
     *
     * @var string
     */
    public $keywords;

    /**
     * Imagem principal para Open Graph/Twitter Cards
     *
     * @var string
     */
    public $image;

    /**
     * Objeto vinil para páginas de produtos
     *
     * @var \App\Models\VinylMaster
     */
    public $vinyl;

    /**
     * Breadcrumbs para dados estruturados
     *
     * @var array
     */
    public $breadcrumbs;

    /**
     * Create a new component instance.
     *
     * @param string|null $title
     * @param string|null $description
     * @param string|null $keywords
     * @param string|null $image
     * @param \App\Models\VinylMaster|null $vinyl
     * @param array|null $breadcrumbs
     * @return void
     */
    public function __construct(
        $title = null,
        $description = null,
        $keywords = null,
        $image = null,
        $vinyl = null,
        $breadcrumbs = null
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->keywords = $keywords;
        $this->image = $image;
        $this->vinyl = $vinyl;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
