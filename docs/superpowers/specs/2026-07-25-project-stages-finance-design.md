# Project Stages & Finance Design

**Date:** 2026-07-25  
**Status:** Approved for implementation

## Goal

Extend projects with admin-defined stages (dates + allocated budget), flexible employee assignment (project and/or stage), company bank accounts, and a full fund-transfer ledger including disbursements to employee bank accounts.

## Decisions

| Topic | Choice |
|-------|--------|
| Bank accounts | Company accounts + disbursement to existing employee bank accounts |
| Stage budget overrun | Allowed only with recorded admin approval (`project_budget_overrides`) |
| Membership | Flexible: project only, stage only, or both |
| Transfer approval | Instant under threshold; Workflow above threshold |
| Delivery | Full feature set in one release (staged technical build order) |

## Data model

- `project_stages` — name, sort_order, start/end dates, allocated_amount, status
- `project_stage_members` — employee ↔ stage
- `company_bank_accounts` — balance, currency, active flag
- `fund_transfers` — polymorphic from/to (company|employee), type, status, project/stage links, audit fields
- `project_budget_overrides` — reason + approver when stages total exceeds project.budget

## Services

- `ProjectStageService`, `ProjectMembershipService`, `CompanyBankAccountService`, `FundTransferService`
- Config: `config/project_finance.php` → `transfer_approval_threshold`
- Workflow type: `fund_transfer` (above threshold); on approve → execute ledger movement

## Permissions

`project-stage-*`, `company-bank-account-*`, `fund-transfer-list|create|approve`, `project-budget-override`

## Out of scope

Real bank APIs, replacing Tasks with stages, full double-entry accounting, employee portal for transfers.
