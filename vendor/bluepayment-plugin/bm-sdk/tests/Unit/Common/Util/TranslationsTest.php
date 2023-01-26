<?php
declare(strict_types=1);

namespace Tests\Unit\Common\Util;

use BlueMedia\Common\Exception\TranslationException;
use BlueMedia\Common\Util\Translations;
use Tests\Unit\BaseTestCase;

class TranslationsTest extends BaseTestCase
{
    private const TRANSLATION = 'Trwa przekierowanie do Bramki Płatniczej Blue Media...';
    private const FALLBACK_TRANSLATION = 'You are being redirected to the Blue Media Payment Gateway...';
    private const TRANSLATION_EXCEPTION = 'Missing required translation key(s): form.test.key';

    public function testGetTranslationReturnsTranslation(): void
    {
        $translations = new Translations();

        $translation = $translations->getTranslation('pl');

        $this->assertNotEmpty($translation);
        $this->assertSame($translation['form.paywall.redirect'], self::TRANSLATION);
    }

    public function testGetTranslationReturnsFallbackTranslation(): void
    {
        $translations = new Translations();

        $translation = $translations->getTranslation('ru');

        $this->assertNotEmpty($translation);
        $this->assertSame($translation['form.paywall.redirect'], self::FALLBACK_TRANSLATION);
    }

    public function testGetTranslationThrowsExceptionOnMissingTranslationKey(): void
    {
        $translations = new Translations();

        $required_translation_keys = [
            'form.test.key'
        ];

        $this->expectException(TranslationException::class);
        $this->expectExceptionMessage(self::TRANSLATION_EXCEPTION);
        $translations->getTranslation('en', $required_translation_keys);
    }
}
