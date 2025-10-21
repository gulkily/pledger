# Plan: Normalize Pledges to 100% Visualization

## Objective
Display pledge commitments so the visual progress pie always sums to 100%, even when total pledged percentage exceeds 100%, while preserving the original pledge amounts for record keeping and communication.

## Current Behavior
- Backend stores raw pledge percentages with no total cap.
- Frontend progress pie uses the raw percentages, so totals above 100% cause the chart to overflow and the total label to exceed 100%.
- Sponsor list displays raw percentages and corresponding min/max dollar estimates.

## Proposed Changes

### 1. Backend (api.php)
- No schema changes required; continue returning raw percentages so admin tools retain exact commitments.
- Consider adding `total_percentage` (already present) to help the frontend compute normalized values without re-summing.

### 2. Frontend (assets/js/app.js & template)
- **Normalization logic**: When pledges load, compute the sum of all pledge percentages. If the sum is 0, keep default behavior. If the sum is > 0, divide each pledge’s percentage by the total to obtain its normalized contribution (fraction of commitments) and multiply by 100 for display in the pie.
- **Chart update**: Feed normalized percentages to the pie chart stroke-dasharray calculations so the progress circle reflects a 0–100 scale regardless of raw totals. Show the formatted normalized total as `100% committed` when sum ≥ 100%; otherwise use actual total.
- **Legend & sponsor list**: Decide between showing normalized percentages, raw percentages, or both. Proposed approach: display raw pledge percentage plus a “(X% of commitments)” note derived from the normalized value, so donors understand both their promise and their share.
- **Total remaining callout**: Replace the current “total percentage” text with two numbers: raw total (“Total pledged: 150%”) and normalized display (“Displayed as 100% pie”). If raw total < 100%, continue showing remaining gap.
- **Copy adjustments**: Add explanatory tooltip or text clarifying that visual slices represent share of total commitments when over-pledged.

### 3. Optional Enhancements
- Add a toggle or info icon that switches between viewing normalized percentages and actual pledged percentages for advanced users/admins.

## Testing Strategy
- **Unit/logic tests** (if adding JS tests): Cover scenarios where pledges sum to <100, exactly 100, and >100 (e.g., 75+75, 60+40+80). Validate normalized output sums to 100 and individual slices match expectations.
- **Manual QA**:
  - Load page with seeded data totaling under, equal to, and over 100% and verify chart rendering and labels.
  - Confirm sponsor list copy communicates both raw and normalized values.
  - Ensure zero-pledge state still shows 0% and empty list messaging.
- **Regression checks**: Submit new pledges and ensure normalization updates without requiring page reload. Confirm caching remains disabled for API calls.

## Deployment Considerations
- No database migrations required. Existing data automatically normalizes on the client.
- Communicate the change to stakeholders/supporters so they understand why the chart may show different percentages compared to past screenshots.
- Rollback is straightforward: revert frontend changes if needed.

## Next Steps
1. Align on how the sponsor list should present normalized vs. raw percentages.
2. Implement frontend normalization logic and updated copy.
3. Review with stakeholders, gather feedback, and iterate on messaging if confusion persists.
