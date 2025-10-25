<div id="create-goal-modal" class="modal-backdrop" aria-hidden="true">
  <div class="modal-card">
    <div class="modal-title">Create goal</div>
    <form id="create-goal-form">
      <div class="modal-row">
        <label>Type</label>
        <select class="select" name="type" required>
          <option value="Long">Long</option>
          <option value="Short">Short</option>
        </select>
      </div>
      <div class="modal-row">
        <label>Title</label>
        <input class="input" type="text" name="goalTitle" placeholder="Goal title" required />
      </div>
      <div class="modal-row">
        <label>Description</label>
        <input class="input" type="text" name="description" placeholder="Optional" />
      </div>
      <div class="modal-row">
        <label>Start</label>
        <input class="input" type="datetime-local" name="startTime" required />
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
        <button type="button" class="goal-card-btn" data-close-modal>Cancel</button>
        <button type="submit" class="goal-card-btn">Create</button>
      </div>
    </form>
  </div>
  
</div>

