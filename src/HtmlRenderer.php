<?php

namespace LaravelManPagination;

use Illuminate\Support\Facades\Request as RequestLF;
use LaravelManPagination\Config\Config;

/**
 * HTML Rendering of the pagination.
 *
 * @link     https://github.com/dev-and-web/laravel-man-pagination
 * @author   Stephen Damian <contact@devandweb.fr>
 * @license  MIT License
 */
class HtmlRenderer extends RendererGenerator
{
    /**
     * @return string
     */
    protected function open(): string
    {
        $html = '';

        $html .= '<nav>';
        $html .=     '<ul class="'.$this->pagination->getCssClassP().'">';

        return $html;
    }

    /**
     * If you are not on the 1st page, display: the left arrow (previous page).
     *
     * @return string
     */
    protected function previousLink(): string
    {
        $html = '';

        $addCss = $this->pagination->isFirstPage() ? ' disabled' : '';

        $href = 'href="'.RequestLF::fullUrlWithQuery([Pagination::PAGE_NAME => ($this->pagination->getCurrentPage() - 1)]).'"';

        $html .= '<li class="page-item'.$addCss.'">';
        $html .=     '<a class="page-link" '.$href.' rel="prev" aria-label="&laquo; '.Config::get('text_previous').'">';
        $html .=         '&lsaquo;';
        $html .=     '</a>';
        $html .= '</li>';

        return $html;
    }

    /**
     * If you are not on the 1st page, make it appear: go to first page.
     *
     * @return string
     */
    protected function firstLink(): string
    {
        $html = '';

        if ($this->pagination->getCurrentPage() !== 1) {
            $points = $this->pagination->getCurrentPage() > ($this->pagination->getNumberLinks() + 2)
                ? '<li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>'
                : '';

            $href = 'href="'.RequestLF::fullUrlWithQuery([Pagination::PAGE_NAME => 1]).'"';

            $html .= '<li class="page-item">';
            $html .=     '<a class="page-link" '.$href.'>';
            $html .=         '1';
            $html .=     '</a>';
            $html .= '</li>';
            $html .= $points;
        }

        return $html;
    }

    /**
     * @param int $nb
     * @return string
     */
    protected function paginationActive(int $nb): string
    {
        return '<li class="page-item active"><span class="page-link">'.$nb.'</span></li>';
    }

    /**
     * @param int $nb
     * @return string
     */
    protected function paginationLink(int $nb): string
    {
        return '<li class="page-item"><a class="page-link" href="'.RequestLF::fullUrlWithQuery([Pagination::PAGE_NAME => $nb]).'">'.$nb.'</a></li>';
    }

    /**
     * If you are not on the last page, display: go to last page.
     *
     * @return string
     */
    protected function lastLink(): string
    {
        $html = '';

        if ($this->pagination->getCurrentPage() !== $this->pagination->getPageEnd()) {
            $points = $this->pagination->getCurrentPage() < $this->pagination->getNbPages() - ($this->pagination->getNumberLinks() + 1)
                ? '<li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>'
                : '';

            $href = 'href="'.RequestLF::fullUrlWithQuery([Pagination::PAGE_NAME => $this->pagination->getNbPages()]).'"';

            $html .= $points;
            $html .= '<li class="page-item">';
            $html .=     '<a class="page-link" '.$href.'>';
            $html .=         $this->pagination->getNbPages();
            $html .=     '</a>';
            $html .= '</li>';
        }

        return $html;
    }

    /**
     * If you are not on the last page, display: the right arrow (next page).
     *
     * @return string
     */
    protected function nextLink(): string
    {
        $html = '';

        $addCss = $this->pagination->isLastPage() ? ' disabled' : '';

        $href = 'href="'.RequestLF::fullUrlWithQuery([Pagination::PAGE_NAME => ($this->pagination->getCurrentPage() + 1)]).'"';

        $html .= '<li class="page-item'.$addCss.'">';
        $html .=     '<a class="page-link" '.$href.' rel="next" aria-label="'.Config::get('text_next').' &raquo;">';
        $html .=         '&rsaquo;';
        $html .=     '</a>';
        $html .= '</li>';

        return $html;
    }

    /**
     * @return string
     */
    protected function close(): string
    {
        $html = '';
        
        $html .=     '</ul>';
        $html .= '</nav>';

        return $html;
    }

    /**
     * @return string
     */
    protected function perPageOnchange(): string
    {
        return 'onchange="document.getElementById(\''.$this->pagination->getCssIdPP().'\').submit()"';
    }

    /**
     * @param string $actionPerPage
     * @return string
     */
    protected function perPageOpenForm(string $actionPerPage): string
    {
        return '<form id="'.$this->pagination->getCssIdPP().'" action="'.$actionPerPage.'" method="get">';
    }

    /**
     * @return string
     */
    protected function perPageLabel(): string
    {
        return '<label for="nb-perpage">'.Config::get('text_per_page').' : </label>';
    }

    /**
     * @param string $onChange
     * @return string
     */
    protected function perPageOpenSelect(string $onChange): string
    {
        return '<select '.$onChange.' name="'.Pagination::PER_PAGE_NAME.'" id="nb-perpage">';
    }

    /**
     * @param string $selected
     * @param string $valuePP
     * @param null|string $all
     * @return string
     */
    protected function perPageOption(string $selected, string $valuePP, string $all = null): string
    {
        $nb = $all !== null ? $all : $valuePP;
        
        return '<option '.$selected.' value="'.$valuePP.'">'.$nb.'</option>';
    }

    /**
     * @return string
     */
    protected function perPageCloseSelect(): string
    {
        return '</select>';
    }

    /**
     * @return string
     */
    protected function perPageCloseForm(): string
    {
        return '</form>';
    }
}
