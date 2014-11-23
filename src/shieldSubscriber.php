<?php
/**
 * @file
 * Contains \Drupal\shield\ShieldSubscriber.
 */
namespace Drupal\shield;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides a ShieldSubscriber.
 */
class ShieldSubscriber implements EventSubscriberInterface {
	/**
	 * // only if KernelEvents::REQUEST !!!
	 * @see Symfony\Component\HttpKernel\KernelEvents for details
	 *
	 * @param Symfony\Component\HttpKernel\Event\GetResponseEvent $event
	 *   The Event to process.
	 */
	public function ShieldLoad(GetResponseEvent $event) {
		$user = \Drupal::config('shield.settings')->get('shield_user');
		if (!$user) {
			return;
		}

		// allow Drush to bypass Shield
		if (PHP_SAPI === 'cli' && \Drupal::config('shield.settings')->get('shield_allow_cli')) {
			return;
		}

		$pass = \Drupal::config('shield.settings')->get('shield_pass');
		if (!empty($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])
			&& $_SERVER['PHP_AUTH_USER'] == $user
			&& $_SERVER['PHP_AUTH_PW']   == $pass) {
			return;
		}

		$print = \Drupal::config('shield.settings')->get('shield_print');
		header(sprintf('WWW-Authenticate: Basic realm="%s"', strtr($print, array('[user]' => $user, '[pass]' => $pass))));
		header('HTTP/1.0 401 Unauthorized');
		exit;
	}

	/**
	 * {@inheritdoc}
	 */
	static function getSubscribedEvents() {
		$events[KernelEvents::REQUEST][] = array('ShieldLoad', 20);
		return $events;
	}
}