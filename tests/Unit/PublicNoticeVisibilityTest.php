<?php

namespace Tests\Unit;

use App\Models\Noticeboard;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class PublicNoticeVisibilityTest extends TestCase
{
    public function test_published_notice_remains_visible_through_its_end_date(): void
    {
        $notice = new Noticeboard([
            'status' => 1,
            'show_on_website' => 1,
            'end_date' => '08/13/2026',
        ]);

        $this->assertTrue($notice->isVisibleOnWebsite(CarbonImmutable::create(2026, 8, 13)));
    }

    public function test_expired_notice_is_hidden(): void
    {
        $notice = new Noticeboard([
            'status' => 1,
            'show_on_website' => 1,
            'end_date' => '08/12/2026',
        ]);

        $this->assertFalse($notice->isVisibleOnWebsite(CarbonImmutable::create(2026, 8, 13)));
    }

    public function test_unpublished_or_inactive_notice_is_hidden(): void
    {
        $unpublishedNotice = new Noticeboard([
            'status' => 1,
            'show_on_website' => 0,
            'end_date' => '08/14/2026',
        ]);
        $inactiveNotice = new Noticeboard([
            'status' => 0,
            'show_on_website' => 1,
            'end_date' => '08/14/2026',
        ]);

        $today = CarbonImmutable::create(2026, 8, 13);

        $this->assertFalse($unpublishedNotice->isVisibleOnWebsite($today));
        $this->assertFalse($inactiveNotice->isVisibleOnWebsite($today));
    }

    public function test_missing_or_malformed_end_date_does_not_hide_published_notice(): void
    {
        $noticeWithoutEndDate = new Noticeboard([
            'status' => 1,
            'show_on_website' => 1,
            'end_date' => '',
        ]);
        $noticeWithMalformedEndDate = new Noticeboard([
            'status' => 1,
            'show_on_website' => 1,
            'end_date' => 'not-a-date',
        ]);

        $today = CarbonImmutable::create(2026, 8, 13);

        $this->assertTrue($noticeWithoutEndDate->isVisibleOnWebsite($today));
        $this->assertTrue($noticeWithMalformedEndDate->isVisibleOnWebsite($today));
    }
}
