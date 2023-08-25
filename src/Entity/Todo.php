<?php
/**
 * Todo entity
 */

namespace App\Entity;

use App\Repository\TodoRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Todo
 */
#[ORM\Entity(repositoryClass: TodoRepository::class)]
class Todo
{
    /**
     * Primary key
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Name
     *
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $name = null;

    /**
     * Description
     *
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Type('string')]
    #[Assert\Length(min: 0, max: 2000)]
    private ?string $description = null;

    /**
     * Todo list
     *
     * @var TodoList|null
     */
    #[ORM\ManyToOne(inversedBy: 'todos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TodoList $todoList = null;

    /**
     * Done
     *
     * @var bool|null
     */
    #[ORM\Column]
    private ?bool $done = null;

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get todo list
     *
     * @return TodoList|null
     */
    public function getTodoList(): ?TodoList
    {
        return $this->todoList;
    }

    /**
     * Set todo list
     *
     * @param TodoList|null $todoList
     * @return $this
     */
    public function setTodoList(?TodoList $todoList): static
    {
        $this->todoList = $todoList;

        return $this;
    }

    /**
     * Is done
     *
     * @return bool|null
     */
    public function isDone(): ?bool
    {
        return $this->done;
    }

    /**
     * Set done
     *
     * @param bool $done
     * @return $this
     */
    public function setDone(bool $done): static
    {
        $this->done = $done;

        return $this;
    }
}
