# Tenants Development Skill

## Overview
The `laravelplus/tenants` package provides multi-tenancy support via organizations. Users can belong to multiple organizations, switch between them, and invite others.

## Key Concepts

### Models
- `Organization` — The tenant entity. Route key is `slug`. Supports soft deletes. Has `owner`, `members` (BelongsToMany), and `invitations` relations.
- `OrganizationMember` — Pivot model with `role` and `joined_at`.
- `OrganizationInvitation` — Tracks invitations with `token`, `expires_at`, `accepted_at`, `declined_at`.

### Architecture
- **Repository**: `OrganizationRepository` implements `OrganizationRepositoryInterface`
- **Service**: `OrganizationService` implements `OrganizationServiceInterface`
- **Events**: 8 event classes (OrganizationCreated, OrganizationDeleted, MemberAdded, MemberRemoved, MemberRoleChanged, InvitationSent, InvitationAccepted, InvitationDeclined)
- **Enums**: `MemberRole` (Owner, Admin, Member), `InvitationStatus` (Pending, Accepted, Declined, Expired)

### Traits
- `HasOrganizations` — Add to User model. Provides `organizations()`, `currentOrganization()`, `switchOrganization()`, `belongsToOrganization()`, etc. Auto-creates personal org on user creation when configured.
- `BelongsToOrganization` — Add to tenant-scoped models. Auto-applies `OrganizationScope` and sets `organization_id` on creation.

### Middleware
- `SetCurrentOrganization` — Resolves current org from session or URL. Sets Spatie team ID.
- `EnsureHasOrganization` — Redirects to org creation if user has none.

### Configuration (`config/tenants.php`)
- `entity_name` / `entity_name_plural` — Display names
- `multi_org` — Allow multiple organizations per user
- `personal_org` — Auto-create personal org on registration
- `routing_mode` — 'session' or 'url'
- `invitation_expiry_hours` — Default 72
- `max_organizations_per_user` — 0 = unlimited
- `max_members_per_organization` — 0 = unlimited
- `member_roles` — ['owner', 'admin', 'member']

### Admin Routes (prefix: `admin/organizations`)
- CRUD for organizations
- Member management (add, remove, change role)

### Web Routes
- `POST organizations/switch/{organization}` — Switch active org
- `GET/POST invitations/{token}/accept` — View/accept invitation
- `POST invitations/{token}/decline` — Decline invitation
- `POST organizations/{organization}/invitations` — Send invitation
- `DELETE organizations/{organization}/invitations/{invitation}` — Cancel invitation

## File Structure
```
packages/laravelplus/tenants/
├── config/tenants.php
├── database/{migrations,factories,seeders}
├── routes/{admin.php,web.php}
├── resources/views/emails/invitation.blade.php
└── src/
    ├── Console/Commands/{InstallCommand,CleanupInvitationsCommand}
    ├── Contracts/{OrganizationRepositoryInterface,OrganizationServiceInterface}
    ├── Enums/{MemberRole,InvitationStatus}
    ├── Events/ (8 event classes)
    ├── Facades/Organization.php
    ├── Http/{Controllers,Middleware,Requests}
    ├── Mail/OrganizationInvitationMail.php
    ├── Models/{Organization,OrganizationMember,OrganizationInvitation}
    ├── Notifications/InvitationReceivedNotification.php
    ├── Repositories/OrganizationRepository.php
    ├── Scopes/OrganizationScope.php
    ├── Services/{OrganizationService,InvitationService}
    ├── Traits/{HasOrganizations,BelongsToOrganization}
    └── TenantsServiceProvider.php
```
