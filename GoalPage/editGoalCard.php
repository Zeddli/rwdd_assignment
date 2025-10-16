<div id="edit-goal-modal" class="modal-backdrop" aria-hidden="true">
  <div class="modal-card">
    <div class="modal-title">Goal details</div>
    <form id="edit-goal-form">
      <input type="hidden" name="goalId" />
      <div class="modal-row">
        <label>Type</label>
        <select class="select" name="type" required>
          <option value="Long">Long</option>
          <option value="Short">Short</option>
        </select>
      </div>
      <div class="modal-row">
        <label>Title</label>
        <input class="input" type="text" name="goalTitle" required />
      </div>
      <div class="modal-row">
        <label>Description</label>
        <input class="input" type="text" name="description" />
      </div>
      <div class="modal-row">
        <label>Start</label>
        <input class="input" type="datetime-local" name="startTime" required />
      </div>
      <div class="modal-row">
        <label>End</label>
        <input class="input" type="datetime-local" name="endTime" required />
      </div>
      <div class="modal-row">
        <label>Deadline</label>
        <input class="input" type="datetime-local" name="deadline" />
      </div>
      <div class="modal-row">
        <label>Status</label>
        <select class="select" name="progress" required>
          <option value="Pending">Pending</option>
          <option value="In Progress">In Progress</option>
          <option value="Completed">Completed</option>
        </select>
      </div>
      <div class="modal-actions">
        <button type="button" id="delete-goal-btn" class="goal-card-btn" style="margin-right:auto;color:#b00020;border-color:#b00020;">Delete</button>
        <button type="button" class="goal-card-btn" data-close-modal>Close</button>
        <button type="submit" class="goal-card-btn">Save</button>
      </div>
    </form>
  </div>
</div>

