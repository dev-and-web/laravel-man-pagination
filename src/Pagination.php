<?php

namespace LaravelManPagination;

use LaravelManPagination\Contracts\PaginationInterface;
use Illuminate\Support\Facades\Request as RequestLF;

/**
 * For generate a pagination.
 *
 * @link     https://github.com/dev-and-web/laravel-man-pagination
 * @author   Stephen Damian <contact@devandweb.fr>
 * @license  MIT License
 */
class Pagination implements PaginationInterface
{
    /**
     * @var null|int
     */
    private ?int $getP = null;

    /**
     * @var null|int|string - If 'all' in URL, it will be a string.
     */
    private $getPP = null;

    /**
     * Number of items per page.
     *
     * @var null|int
     */
    private ?int $perPage = null;

    /**
     * Total number of pages.
     *
     * @var int
     */
    private int $nbPages;

    /**
     * Current page.
     *
     * @var int
     */
    private int $currentPage;

    /**
     * Start page.
     *
     * @var int
     */
    private int $pageStart;

    /**
     * End page.
     *
     * @var int
     */
    private int $pageEnd;

    /**
     * The <select> options for per page <form>.
     *
     * @var array
     */
    private array $arrayOptionsSelect = [];

    /**
     * OFFSET - from where we start the LIMIT.
     *
     * @var null|int
     */
    private ?int $offset;

    /**
     * LIMIT - number of items to retrieve.
     *
     * @var null|int
     */
    private ?int $limit;

    /**
     * Number of elements on which to perform pagination.
     *
     * @var int
     */
    private int $count;

    /**
     * Number of items per page by default.
     *
     * @var int
     */
    private int $defaultPerPage;

    /**
     * Number of links next to the current page.
     *
     * @var int
     */
    private int $numberLinks;

    /**
     * CSS pagination class.
     *
     * @var string
     */
    private string $cssClassP;

    /**
     * CSS ID of the "per page" of the pagination.
     *
     * @var string
     */
    private string $cssIdPP;

    /**
     * @var HtmlRenderer
     */
    private HtmlRenderer $htmlRenderer;

    /**
     * @const string
     */
    const PAGE_NAME = 'page';

    /**
     * @const string
     */
    const PER_PAGE_NAME = 'pp';

    /**
     * @const string
     */
    const PER_PAGE_OPTION_ALL = 'all';

    /**
     * @const string
     */
    const REGEX_INTEGER = '/^[0-9]+$/';
    
    /**
     * Pagination constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (RequestLF::has(self::PAGE_NAME)) {
            $this->getP = (int) round(RequestLF::query(self::PAGE_NAME));
        }

        if (RequestLF::has(self::PER_PAGE_NAME)) {
            $this->getPP = RequestLF::query(self::PER_PAGE_NAME) === self::PER_PAGE_OPTION_ALL
                ? RequestLF::query(self::PER_PAGE_NAME)
                : (int) RequestLF::query(self::PER_PAGE_NAME);
        }

        $this->defaultPerPage = isset($options['pp']) && is_integer($options['pp']) ? $options['pp'] : 15;
        $this->numberLinks = isset($options['number_links']) && is_integer($options['number_links']) ? $options['number_links'] : 10;
        $this->arrayOptionsSelect = isset($options['options_select']) && is_array($options['options_select']) ? $options['options_select'] : [15, 30, 50, 100, 200, 300];

        $this->cssClassP = isset($options['css_class_p']) && is_string($options['css_class_p']) ? $options['css_class_p'] : 'pagination';
        $this->cssIdPP = isset($options['css_id_pp']) && is_string($options['css_id_pp']) ? $options['css_id_pp'] : 'per-page';

        $this->htmlRenderer = new HtmlRenderer($this);
    }

    /**
     * Activate pagination.
     *
     * @param int $count - Number of elements to paginate.
     */
    public function paginate(int $count): void
    {
        $this->count = $count;

        $this->treatmentPerPage();
                        
        if ($this->perPage !== null) {
            $this->nbPages = ceil($this->count / $this->perPage);
        } else {
            $this->nbPages = 1;
        }

        if ($this->getP !== null && $this->getP > 0 && $this->getP <= $this->nbPages && preg_match(self::REGEX_INTEGER, $this->getP)) {
            $this->currentPage = $this->getP;
        } else {
            $this->currentPage = 1;
        }

        $this->setLimitAndSetOffset();
    }

    /**
     * Processing the number of items per page (for <select>)
     */
    private function treatmentPerPage(): void
    {
        if ($this->getPP !== null && (preg_match(self::REGEX_INTEGER, $this->getPP) || $this->getPP === self::PER_PAGE_OPTION_ALL)) {
            if (in_array($this->getPP, $this->arrayOptionsSelect)) {
                if ($this->getPP === self::PER_PAGE_OPTION_ALL) {
                    $this->perPage = null;
                    $this->getP = 1;
                } else {
                    $this->perPage = (int) round($this->getPP);
                }
            } else {
                $this->perPage = $this->defaultPerPage;
            }
        } else {
            $this->perPage = $this->defaultPerPage;
        }
    }

    /**
     * For assign the limit and the offset
     */
    private function setLimitAndSetOffset(): void
    {
        if ($this->perPage === null) {
            $this->offset = null;
            $this->limit = null;
        } else {
            $this->offset = ($this->currentPage - 1) * $this->perPage;
            $this->limit = $this->perPage;
        }
    }

    /**
     * @return null|int - OFFSET
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @return null|int - LIMIT
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return int - Total number of elements on which to paginate.
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return int - Number of items on the current page.
     */
    public function getCountOnCurrentPage(): int
    {
        if ($this->count < $this->perPage || $this->perPage === null) {
            return $this->count;
        } else {
            if ($this->hasMorePages()) {
                return $this->perPage;
            } else {
                return $this->getCountOnLastPage();
            }
        }
    }

    /**
     * To return the indexing of the first element on the current page.
     * items "nb start" à ...
     *
     * @return int
     */
    public function getFrom(): int
    {
        return $this->getFromTo()['from'];
    }

    /**
     * To return the indexing of the last element on the current page.
     * items ... à "nb end"
     *
     * @return int
     */
    public function getTo(): int
    {
        return $this->getFromTo()['to'];    
    }

    /**
     * To return the indexing of the first element and the indexing of the last element on the current page.
     * items "nb start" to "nb end" on this page
     *
     * @return array - Array associatif
     *    'from' => nb start
     *    'to' => nb end
     */
    private function getFromTo(): array
    {
        if ($this->count < $this->perPage || $this->perPage === null) {
            $start = 1;
            $end = $this->count;
        } else {
            if ($this->hasMorePages()) {
                $end = $this->perPage * $this->currentPage;
                $start = ($end - $this->perPage) + 1;
            } else {
                $endTest = $this->perPage * $this->currentPage;
                $start = ($endTest - $this->perPage) + 1;

                $end = $start + $this->getCountOnLastPage();
            }
        }

        return ['from' => $start, 'to' => $end];
    }

    /**
     * @return int - The number of items on the last page.
     */
    private function getCountOnLastPage(): int
    {
        $a = $this->perPage * $this->nbPages;
        $b = $a - $this->count;
        $c = $this->perPage - $b;

        return $c;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getNbPages(): int
    {
        return $this->nbPages;
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage !== null ? $this->perPage : '';
    }

    /**
     * @return int
     */
    public function getDefaultPerPage()
    {
        return $this->defaultPerPage !== null ? $this->defaultPerPage : '';
    }

    /**
     * @return null|int|string
     */
    public function getGetPP()
    {
        return $this->getPP;
    }

    /**
     * @return int
     */
    public function getPageStart(): int
    {
        return $this->pageStart;
    }

    /**
     * @return int
     */
    public function getPageEnd(): int
    {
        return $this->pageEnd;
    }

    /**
     * @return int
     */
    public function getNumberLinks(): int
    {
        return $this->numberLinks;
    }

    /**
     * @return string
     */
    public function getCssClassP(): string
    {
        return $this->cssClassP;
    }

    /**
     * @return string
     */
    public function getCssIdPP(): string
    {
        return $this->cssIdPP;
    }

    /**
     * @return array
     */
    public function getArrayOptionsSelect(): array
    {
        return $this->arrayOptionsSelect;
    }

    /**
     * @return bool - True if there are pages left after the current one.
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->nbPages; 
    }

    /**
     * @return bool - True if we are on the first page.
     */
    public function isFirstPage(): bool
    {
        if (RequestLF::has(self::PAGE_NAME)) {
            return $this->getP === 1; 
        }
        
        return true;
    }

    /**
     * @return bool - True if we are on the last page.
     */
    public function isLastPage(): bool
    {
        return $this->getP === $this->nbPages; 
    }

    /**
     * Render the pagination as HTML.
     *
     * @return string
     */
    public function render(): string
    {
        $this->setPageStart()->setPageEnd();

        return $this->htmlRenderer->render();
    }

    /**
     * "Limit the start". pageStart, any clickable links that will be after the current page.
     *
     * @return $this
     */
    private function setPageStart()
    {
        $firstPage = $this->currentPage - $this->numberLinks;

        if ($firstPage >= 1) {
            $this->pageStart = $firstPage;
        } else {
            $this->pageStart = 1;
        }

        return $this;
    }

    /**
     * "Limit the end". pageEnd, any clickable links that will be before the current page.
     */
    private function setPageEnd()
    {
        $lastPage = $this->currentPage + $this->numberLinks;

        if ($lastPage <= $this->nbPages) {
            $this->pageEnd = $lastPage;
        } else {
            $this->pageEnd = $this->nbPages;
        }
    }

    /**
     * Render the "per page" in HTML format.
     *
     * @param array $options
     *     $options['action'] (string) : for the action of the form.
     * @return string
     */
    public function perPage(array $options = []): string
    {
        return $this->htmlRenderer->perPage($options);
    }
}
