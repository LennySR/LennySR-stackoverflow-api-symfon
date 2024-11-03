<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\QueryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: QueryRepository::class)]
#[ORM\Index(name: "tagged_idx", columns: ["tagged"])] // Índice en tagged
#[ORM\Index(name: "toDate_idx", columns: ["from_Date"])] // Índice en toDate
#[ORM\Index(name: "toDate_idx", columns: ["to_Date"])] // Índice en toDate
class Query
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $tagged;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $fromDate;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $toDate;

    #[ORM\OneToMany(mappedBy: 'query', targetEntity: Question::class, cascade: ['persist'])]
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTagged()
    {
        return $this->tagged;
    }

    /**
     * @param string $tagged
     */
    public function setTagged($tagged)
    {
        $this->tagged = $tagged;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * @param \DateTimeInterface|null $fromDate
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * @param \DateTimeInterface|null $toDate
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setQuery($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // Establecer el propietario a null si es necesario
            if ($question->getQuery() === $this) {
                $question->setQuery(null);
            }
        }

        return $this;
    }
}
