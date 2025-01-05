<?php

class AvailableActions
{
    // Константы статусов задания
    const STATUS_NEW = 'new';           // Задание только что создано
    const STATUS_IN_PROGRESS = 'progress'; // Задание выполняется
    const STATUS_CANCEL = 'cancel';    // Задание отменено
    const STATUS_COMPLETE = 'complete';// Задание завершено
    const STATUS_EXPIRED = 'expired';  // Задание просрочено

    // Константы действий
    const ACTION_RESPONSE = 'act_response';  // Откликнуться
    const ACTION_CANCEL = 'act_cancel';      // Отменить задание
    const ACTION_DENY = 'act_deny';          // Отказаться от задания
    const ACTION_COMPLETE = 'act_complete'; // Завершить задание

    // Свойства класса
    private ?int $performerId; // ID исполнителя (может быть null)
    private int $clientId;     // ID заказчика
    private $status;           // Текущий статус задания

    // Конструктор класса
    public function __construct(string $status, int $clientId, ?int $performerId = null)
    {
        // Устанавливаем текущий статус через метод setStatus
        $this->setStatus($status);

        // Устанавливаем ID заказчика и исполнителя
        $this->clientId = $clientId;
        $this->performerId = $performerId;
    }

    // Возвращает карту статусов (код статуса -> название)
    public function getStatusesMap(): array
    {
        return [
            self:: STATUS_NEW => 'Новое',
            self:: STATUS_CANCEL => 'Отменено',
            self:: STATUS_IN_PROGRESS => 'В работе',
            self:: STATUS_COMPLETE => 'Выполнено',
            self:: STATUS_EXPIRED => 'Провалено',
        ];
    }

    // Возвращает карту действий (код действия -> название)
    public function getActionsMap(): array
    {
        return [
            self:: ACTION_CANCEL => 'Отменить',
            self:: ACTION_RESPONSE => 'Откликнуться',
            self:: ACTION_COMPLETE => 'Выполнено',
            self:: ACTION_DENY => 'Отказаться',
        ];
    }

    // Определяет следующий статус после выполнения действия
    public function getNextStatus(string $action): ?string
    {
        $map = array(
            self:: ACTION_COMPLETE => self:: STATUS_COMPLETE,
            self::ACTION_CANCEL => self:: STATUS_CANCEL,
            self::ACTION_DENY => self:: STATUS_CANCEL,
        );

        // Возвращаем статус, если он есть в карте, или null
        return $map[$action] ?? null;
    }

    // Устанавливает текущий статус задания
    private function setStatus(string $status): void
    {
        // Список всех доступных статусов
        $availableStatuses = [
            self:: STATUS_NEW,
            self:: STATUS_IN_PROGRESS,
            self:: STATUS_CANCEL,
            self:: STATUS_COMPLETE,
            self:: STATUS_EXPIRED
        ];

        // Проверяем, есть ли переданный статус в списке доступных
        if (in_array($status, $availableStatuses)) {
            $this->status = $status;
        }
        // ПРОБЛЕМА: Если статус недоступен, код просто молча игнорирует это.
        // Лучше выбрасывать исключение или логировать ошибку.
        if (!in_array($status, $availableStatuses)) {
            throw new InvalidArgumentException("Недопустимый статус: $status");
        }

    }

    // Возвращает список доступных действий для конкретного статуса
    private function statusAllowedActions(string $status): array
    {
        $map = [
            self:: STATUS_IN_PROGRESS =>
                [
                    self::ACTION_DENY, self::ACTION_COMPLETE
                ],
            self::STATUS_NEW =>
                [
                    self:: ACTION_CANCEL, self:: ACTION_RESPONSE
                ],
        ];

        // Возвращаем список доступных действий или пустой массив
        return $map[$status] ?? [];
    }
}
