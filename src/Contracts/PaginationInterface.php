<?php

namespace LaravelManPagination\Contracts;

/**
 * @link     https://github.com/dev-and-web/laravel-man-pagination
 * @author   Stephen Damian <contact@devandweb.fr>
 * @license  MIT License
 */
Interface PaginationInterface
{
    public function __construct(array $options = []);
    
    public function paginate(int $count): void;

    public function getOffset(): ?int;

    public function getLimit(): ?int;

    public function getCount(): int;

    public function getCountOnCurrentPage(): int;

    public function getFrom(): int;

    public function getTo(): int;

    public function getCurrentPage(): int;

    public function getNbPages(): int;

    public function getPerPage();

    public function hasMorePages(): bool;

    public function isFirstPage(): bool;

    public function isLastPage(): bool;

    public function render(): string;

    public function perPage(array $options = []): string;
}
