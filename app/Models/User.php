<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'school_year',
        'student_number',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user can approve a specific workflow stage.
     *
     * @param \App\Models\WorkflowStage $stage
     * @return bool
     */
    public function canApproveStage($stage): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->role === $stage->assigned_role;
    }

    /**
     * Relationship: Concept papers created by this user.
     */
    public function conceptPapers()
    {
        return $this->hasMany(\App\Models\ConceptPaper::class, 'requisitioner_id');
    }

    /**
     * Relationship: Workflow stages assigned to this user.
     */
    public function assignedStages()
    {
        return $this->hasMany(\App\Models\WorkflowStage::class, 'assigned_user_id');
    }

    /**
     * Relationship: Audit logs created by this user.
     */
    public function auditLogs()
    {
        return $this->hasMany(\App\Models\AuditLog::class, 'user_id');
    }
}
