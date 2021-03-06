<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity("email", message="L'email est déjà utilisé sur ce site")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="username", type="string", length=31)
     * @Assert\NotBlank
     */
    private $username;

    /**
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\Email
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Les mots de passes tappés sont différents")
     */
    private $confirm_password;

    /**
     * @ORM\Column(type="string", length=31)
     * @Assert\NotBlank
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=31)
     * @Assert\NotBlank
     */
    private $lastName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $promo;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity=Validation::class, mappedBy="author")
     */
    private $validations;

    /**
     * @ORM\ManyToMany(targetEntity=Course::class, mappedBy="teachers")
     */
    private $coursesTeached;

    /**
     * @ORM\ManyToMany(targetEntity=Course::class, mappedBy="students")
     */
    private $coursesFollowed;


    public function __construct()
    {
        $this->validations = new ArrayCollection();
        $this->coursesTeached = new ArrayCollection();
        $this->coursesFollowed = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getConfirmPassword()
    {
        return $this->confirm_password;
    }

    public function setConfirmPassword($confirm_password): void
    {
        $this->confirm_password = $confirm_password;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPromo(): ?int
    {
        return $this->promo;
    }

    public function setPromo(int $promo): self
    {
        $this->promo = $promo;

        return $this;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // guarantee every user at least has ROLE_USER

        return array_unique($roles);
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection|Validation[]
     */
    public function getValidations(): Collection
    {
        return $this->validations;
    }

    public function addValidation(Validation $validation): self
    {
        if (!$this->validations->contains($validation)) {
            $this->validations[] = $validation;
            $validation->setAuthor($this);
        }

        return $this;
    }

    public function removeValidation(Validation $validation): self
    {
        if ($this->validations->removeElement($validation)) {
            // set the owning side to null (unless already changed)
            if ($validation->getAuthor() === $this) {
                $validation->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Course[]
     */
    public function getCoursesTeached(): Collection
    {
        return $this->coursesTeached;
    }

    public function addCoursesTeached(Course $coursesTeached): self
    {
        if (!$this->coursesTeached->contains($coursesTeached)) {
            $this->coursesTeached[] = $coursesTeached;
            $coursesTeached->addTeacher($this);
        }

        return $this;
    }

    public function removeCoursesTeached(Course $coursesTeached): self
    {
        if ($this->coursesTeached->removeElement($coursesTeached)) {
            $coursesTeached->removeTeacher($this);
        }

        return $this;
    }

    /**
     * @return Collection|Course[]
     */
    public function getCoursesFollowed(): Collection
    {
        return $this->coursesFollowed;
    }

    public function addCoursesFollowed(Course $coursesFollowed): self
    {
        if (!$this->coursesFollowed->contains($coursesFollowed)) {
            $this->coursesFollowed[] = $coursesFollowed;
            $coursesFollowed->addStudent($this);
        }

        return $this;
    }

    public function removeCoursesFollowed(Course $coursesFollowed): self
    {
        if ($this->coursesFollowed->removeElement($coursesFollowed)) {
            $coursesFollowed->removeStudent($this);
        }

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }
}
