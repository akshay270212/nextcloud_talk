<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Theming\Service;

use OCA\Theming\ITheme;
use OCA\Theming\Themes\DefaultTheme;
use OCA\Theming\Themes\LightTheme;
use OCA\Theming\Themes\DarkTheme;
use OCA\Theming\Themes\DarkHighContrastTheme;
use OCA\Theming\Themes\HighContrastTheme;
use OCA\Theming\Themes\DyslexiaFont;
use OCA\Theming\ImageManager;
use OCA\Theming\ThemingDefaults;
use OCA\Theming\Util;
use OCP\App\IAppManager;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserSession;

class ThemesService {

	/** @var ITheme[] */
	private array $themes = [];

	public function __construct(
		private IUserSession $userSession,
		private IConfig $config,
		private Util $util,
		private ThemingDefaults $themingDefaults,
		private IURLGenerator $urlGenerator,
		private ImageManager $imageManager,
		private IL10N $l,
		private IAppManager $appManager,
		private ?IRequest $request = null,
	) {
		$this->initializeThemes();
	}

	private function initializeThemes(): void {
		$this->themes = [
			'default' => new DefaultTheme(
				$this->util,
				$this->themingDefaults,
				$this->userSession,
				$this->urlGenerator,
				$this->imageManager,
				$this->config,
				$this->l,
				$this->appManager,
				$this->request
			),
			'light' => new LightTheme(
				$this->util,
				$this->themingDefaults,
				$this->userSession,
				$this->urlGenerator,
				$this->imageManager,
				$this->config,
				$this->l,
				$this->appManager,
				$this->request
			),
			'dark' => new DarkTheme(
				$this->util,
				$this->themingDefaults,
				$this->userSession,
				$this->urlGenerator,
				$this->imageManager,
				$this->config,
				$this->l,
				$this->appManager,
				$this->request
			),
			'dark-highcontrast' => new DarkHighContrastTheme(
				$this->util,
				$this->themingDefaults,
				$this->userSession,
				$this->urlGenerator,
				$this->imageManager,
				$this->config,
				$this->l,
				$this->appManager,
				$this->request
			),
			'highcontrast' => new HighContrastTheme(
				$this->util,
				$this->themingDefaults,
				$this->userSession,
				$this->urlGenerator,
				$this->imageManager,
				$this->config,
				$this->l,
				$this->appManager,
				$this->request
			),
			'opendyslexic' => new DyslexiaFont(
				$this->util,
				$this->themingDefaults,
				$this->userSession,
				$this->urlGenerator,
				$this->imageManager,
				$this->config,
				$this->l,
				$this->appManager,
				$this->request
			),
		];
	}

	/**
	 * @return ITheme[]
	 */
	public function getThemes(): array {
		return $this->themes;
	}

	/**
	 * @return string[]
	 */
	public function getEnabledThemes(): array {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return ['default']; // Return default theme for anonymous users
		}

		$enforcedTheme = $this->config->getSystemValueString('enforce_theme', '');
		if ($enforcedTheme !== '') {
			return [$enforcedTheme];
		}

		$enabledThemes = [];
		foreach ($this->themes as $theme) {
			if ($this->isThemeEnabled($theme, $user)) {
				$enabledThemes[] = $theme->getId();
			}
		}

		// If no themes are enabled, return default
		if (empty($enabledThemes)) {
			$enabledThemes[] = 'default';
		}

		return $enabledThemes;
	}

	public function enableTheme(ITheme $theme): void {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return;
		}

		$this->config->setUserValue($user->getUID(), 'theming', (string)$theme->getType(), $theme->getId());
	}

	public function disableTheme(ITheme $theme): void {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return;
		}

		$this->config->deleteUserValue($user->getUID(), 'theming', (string)$theme->getType());
	}

	private function isThemeEnabled(ITheme $theme, IUser $user): bool {
		$currentTheme = $this->config->getUserValue($user->getUID(), 'theming', (string)$theme->getType(), '');
		return $currentTheme === $theme->getId();
	}
}
