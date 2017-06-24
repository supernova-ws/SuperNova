<?php
/**
 * Created by Gorlum 22.06.2017 16:50
 */

namespace Event;

use Common\GlobalContainer;

/**
 * Class EventBus
 * @package Event
 */
class EventBus {

  /**
   * @var IObserver[][] $subscriptions
   */
  protected $subscriptions;

  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;
  }

  /**
   * @param IObserver $observer
   * @param int       $eventId
   *
   * @return bool|mixed - false if observer not subscribed to event or observer subscription ID
   */
  public function haveSubscription(IObserver $observer, $eventId) {
    if (!is_array($this->subscriptions[$eventId])) {
      return false;
    }

    return array_search($observer, $this->subscriptions[$eventId]);
  }

  protected function unSubscribeFromOne(IObserver $observer, $eventId) {
    if (($foundKey = $this->haveSubscription($observer, $eventId)) !== false) {
      unset($this->subscriptions[$eventId][$foundKey]);
    }
  }

  /**
   * Unsubscribes observer from all events
   *
   * @param IObserver $observer
   */
  protected function unSubscribeFromAll(IObserver $observer) {
    foreach ($this->subscriptions as $eventId => $cork) {
      $this->unSubscribeFromOne($observer, $eventId);
    }
  }


  public function subscribe(IObserver $observer, $eventId = EVENT_ALL) {
    // In case of global subscription removing secondary subscriptions to avoid duplicate calls
    if ($eventId === EVENT_ALL) {
      $this->unSubscribeFromAll($observer);
    } elseif ($this->haveSubscription($observer, $eventId) !== false) {
      // Observer already subscribed
      return;
    }

    !isset($this->subscriptions[$eventId]) ? $this->subscriptions[$eventId] = [] : false;
    $this->subscriptions[$eventId][] = $observer;
  }

  public function unSubscribe($observer, $eventId = EVENT_ALL) {
    if ($eventId === EVENT_ALL) {
      $this->unSubscribeFromAll($observer);
    } elseif ($this->haveSubscription($observer, $eventId) !== false) {
      $this->unSubscribeFromOne($observer, $eventId);
    }
  }

  public function dispatch(Event $event) {
    $subscriptions = empty($this->subscriptions[$event->getType()]) ? [] : $this->subscriptions[$event->getType()];
    $subscriptions = $this->mergeEventAllObservers($subscriptions);

    if (empty($subscriptions)) {
      return;
    }

    $methodName = STR_OBSERVER_ENTRY_METHOD_NAME;
    foreach ($subscriptions as $observer) {
      if (is_object($observer) && method_exists($observer, $methodName)) {
        $observer->$methodName($event);
      }
    }

  }

  /**
   * @param IObserver[] $typeSubscriptions
   *
   * @return IObserver[]
   */
  protected function mergeEventAllObservers($typeSubscriptions) {
    $eventAllObservers = empty($this->subscriptions[EVENT_ALL]) ? [] : $this->subscriptions[EVENT_ALL];
    foreach ($eventAllObservers as $object) {
      if (!array_search($object, $typeSubscriptions)) {
        $typeSubscriptions[] = $object;
      }
    }

    return $typeSubscriptions;
  }

}
