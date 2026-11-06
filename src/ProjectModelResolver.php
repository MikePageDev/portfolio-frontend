<?php

namespace MikePageDev\PortfolioFrontend;

use Illuminate\Database\Eloquent\Model;

class ProjectModelResolver
{
    /**
     * @param  list<string>  $candidates  Class names to try, most preferred first.
     */
    public function __construct(private array $candidates) {}

    /**
     * @return list<string>
     */
    public function candidates(): array
    {
        return $this->candidates;
    }

    /**
     * @return class-string<Model>
     *
     * @throws MissingProjectModel
     */
    public function resolve(): string
    {
        foreach ($this->candidates as $candidate) {
            if (is_subclass_of($candidate, Model::class)) {
                return $candidate;
            }
        }

        throw new MissingProjectModel;
    }
}
