<?php

namespace LaravelManPagination;

use Illuminate\Support\Facades\Request as RequestLF;
use LaravelManPagination\Config\Lang;
use LaravelManPagination\Config\Config;
use LaravelManPagination\Support\String\Str;
use LaravelManPagination\Contracts\PaginationInterface;

/**
 * Rendering of the pagination.
 *
 * @link     https://github.com/dev-and-web/laravel-man-pagination
 * @author   Stephen Damian <contact@devandweb.fr>
 * @license  MIT License
 */
abstract class RendererGenerator
{
    /**
     * @var PaginationInterface
     */
    protected PaginationInterface $pagination;

    /**
     * RenderGenerator constructor.
     *
     * @param PaginationInterface $pagination
     */
    public function __construct(PaginationInterface $pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * To display the pagination.
     *
     * @return string
     */
    public function render(): string
    {
        $html = '';

        if ($this->pagination->getGetPP() !== Pagination::PER_PAGE_OPTION_ALL && $this->pagination->getCount() > $this->pagination->getPerPage()) {
            $html .= $this->open();

            $html .= $this->previousLink();
            $html .= $this->firstLink();

            for ($i = $this->pagination->getPageStart(); $i <= $this->pagination->getPageEnd(); $i++) {
                if ($i === $this->pagination->getCurrentPage()) {
                    $html .= $this->paginationActive($i);
                } else {
                    if ($i !== 1 && $i !== $this->pagination->getNbPages()) {
                        $html .= $this->paginationLink($i);
                    }
                }
            }

            $html .= $this->lastLink();
            $html .= $this->nextLink();

            $html .= $this->close();
        }

        return $html;
    }

    /**
     * To choose the number of items to display per page.
     *
     * @param array $options
     *     $options['action'] (string) : for the action of the form.
     * @return string
     */
    public function perPage(array $options = []): string
    {
        $html = '';

        if ($this->pagination->getCount() > $this->pagination->getDefaultPerPage()) {
            $actionPerPage = isset($options['action']) ? $options['action'] : RequestLF::url();

            $onChange = !RequestLF::ajax() ? $this->perPageOnchange() : '';

            $html .= $this->perPageOpenForm($actionPerPage);
            $html .= $this->perPageLabel();
            $html .= $this->perPageOpenSelect($onChange);    

            foreach ($this->pagination->getArrayOptionsSelect() as $valuePP) {
                $html .= $this->generateOption($valuePP);
            }

            $html .= $this->perPageCloseSelect();
            $html .= Str::inputHiddenIfHasQueryString(['except' => [Pagination::PER_PAGE_NAME]]);
            $html .= $this->perPageCloseForm();
        }

        return $html;
    }

    /**
     * @param int|string $valuePP - If 'all' in URL, it will be a string.
     * @return string
     */
    private function generateOption($valuePP): string
    {
        $html = '';

        $selectedPP = $valuePP === $this->pagination->getGetPP()
            ? 'selected'
            : '';

        $selectedDefault = $this->pagination->getGetPP() === null && $valuePP === $this->pagination->getDefaultPerPage()
            ? 'selected'
            : '';

        if (
            $this->pagination->getCount() >= $valuePP &&
            $valuePP !== $this->pagination->getDefaultPerPage() &&
            $valuePP !== Pagination::PER_PAGE_OPTION_ALL
        ) {
            $html .= $this->perPageOption($selectedDefault.$selectedPP, $valuePP);
        } elseif ($valuePP === $this->pagination->getDefaultPerPage() || $valuePP === Pagination::PER_PAGE_OPTION_ALL) {
            if ($valuePP === Pagination::PER_PAGE_OPTION_ALL) {
                $html .= $this->perPageOption($selectedDefault.$selectedPP, $valuePP, Config::get('text_all'));
            } else {
                $html .= $this->perPageOption($selectedDefault.$selectedPP, $valuePP);
            }
        }

        return $html;
    }
}
