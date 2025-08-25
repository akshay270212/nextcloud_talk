<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Dashboard\AppInfo;

use OCP\App\IAppManager;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\INavigationManager;
use OCP\IURLGenerator;
use OCP\IUserSession;
use OCP\L10N\IFactory;

class Application extends App implements IBootstrap {

	public const APP_ID = 'dashboard';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public function register(IRegistrationContext $context): void {
		// Register navigation entries
	}

	public function boot(IBootContext $context): void {
		$context->injectFn(function (INavigationManager $navigationManager) {
			$this->registerNavigationLinks($navigationManager);
		});
	}

	public function registerNavigationLinks(INavigationManager $navigationManager): void {
		$userSession = \OC::$server->get(IUserSession::class);
		$urlGenerator = \OC::$server->get(IURLGenerator::class);
		$l10nFactory = \OC::$server->get(IFactory::class);
		$l = $l10nFactory->get(self::APP_ID);
		
		$user = $userSession->getUser();
		if ($user === null) {
			return; // Don't add navigation for guests
		}

		// Projects navigation entry (appears after Calendar which has order 5)
		$navigationManager->add(function() use ($urlGenerator, $l) {
			return [
				'id' => 'projects',
				'name' => $l->t('Projects'),
				'href' => 'https://projects.lockated.com/projects',
				'icon' => $urlGenerator->imagePath('core', 'places/folder-projects.svg'),
				'order' => 6,
				'type' => 'link'
			];
		});

		// Tasks navigation entry
		$navigationManager->add(function() use ($urlGenerator, $l) {
			return [
				'id' => 'tasks',
				'name' => $l->t('Tasks'),
				'href' => 'https://projects.lockated.com/tasks',
				'icon' => $urlGenerator->imagePath('core', 'actions/checkmark.svg'),
				'order' => 7,
				'type' => 'link'
			];
		});

		// MOM (Minutes of Meeting) navigation entry
		$navigationManager->add(function() use ($urlGenerator, $l) {
			return [
				'id' => 'mom',
				'name' => $l->t('MOM'),
				'href' => 'https://projects.lockated.com/mom',
				'icon' => $urlGenerator->imagePath('core', 'actions/edit.svg'),
				'order' => 8,
				'type' => 'link'
			];
		});

		// Sprint Pages navigation entry
		$navigationManager->add(function() use ($urlGenerator, $l) {
			return [
				'id' => 'sprint',
				'name' => $l->t('Sprint'),
				'href' => 'https://projects.lockated.com/sprint',
				'icon' => $urlGenerator->imagePath('core', 'actions/play.svg'),
				'order' => 9,
				'type' => 'link'
			];
		});
	}
}
