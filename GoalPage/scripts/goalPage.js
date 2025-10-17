(() => {
  const api = {
    getGoals: () => fetch(`backend/getGoal.php`).then((r) => r.json()),
    createGoal: (payload) =>
      fetch("backend/createGoal.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      }).then((r) => r.json()),
    updateGoal: (payload) =>
      fetch("backend/updateGoal.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      }).then((r) => r.json()),
    deleteGoal: (goalId) =>
      fetch("backend/deleteGoal.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ goalId }),
      }).then((r) => r.json()),
  };

  const els = {
    longRow: document.getElementById("long-goal-row"),
    shortRow: document.getElementById("short-goal-row"),
    createBtn: document.getElementById("create-goal-btn"),
    createModal: document.getElementById("create-goal-modal"),
    editModal: document.getElementById("edit-goal-modal"),
  };

  const state = {
    workspaceId: null, // Will be set from session via backend
    goals: [],
  };

  function formatDateRange(start, end) {
    try {
      const s = new Date(start);
      const e = new Date(end);
      const fmt = (d) =>
        d.toLocaleDateString(undefined, {
          year: "numeric",
          month: "long",
          day: "numeric",
        });
      return `${fmt(s)} → ${fmt(e)}`;
    } catch (_) {
      return `${start} → ${end}`;
    }
  }

  function render() {
    console.log('Rendering goals:', state.goals);
    const long = state.goals.filter((g) => g.Type === "Long");
    const short = state.goals.filter((g) => g.Type === "Short");
    console.log('Long goals:', long);
    console.log('Short goals:', short);
    const createCard = (g) => {
      const div = document.createElement("div");
      div.className = "goal-card";
      div.setAttribute("role", "listitem");
      const endDisplay =
        g.EndTime && g.Progress === "Completed"
          ? formatDateRange(g.StartTime, g.EndTime)
          : `${formatDateRange(g.StartTime, g.StartTime).split(" → ")[0]} → —`;
      div.innerHTML = `
        <div class="goal-card-title">🎯 <span>${escapeHtml(
          g.GoalTitle || "Untitled"
        )}</span></div>
        <div class="goal-card-status">Status: ${escapeHtml(g.Progress)}</div>
        <div class="goal-card-daterange">Date range:<br>${endDisplay}</div>
        <div class="goal-card-actions">
          <button class="goal-card-btn" data-action="open-edit" data-id="${
            g.GoalID
          }">Open</button>
        </div>
      `;
      return div;
    };
    if (long.length === 0) {
      const empty = document.createElement("div");
      empty.className = "empty-state";
      empty.textContent = "No goal yet. Please create a goal";
      els.longRow.replaceChildren(empty);
    } else {
      els.longRow.replaceChildren(...long.map(createCard));
    }

    if (short.length === 0) {
      const empty = document.createElement("div");
      empty.className = "empty-state";
      empty.textContent = "No goal yet. Please create a goal";
      els.shortRow.replaceChildren(empty);
    } else {
      els.shortRow.replaceChildren(...short.map(createCard));
    }
  }

  function escapeHtml(str) {
    return String(str ?? "").replace(
      /[&<>"']/g,
      (m) =>
        ({
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          '"': "&quot;",
          "'": "&#39;",
        }[m])
    );
  }

  async function load() {
    console.log('Loading goals...');
    const res = await api.getGoals().catch((error) => {
      console.error('Error loading goals:', error);
      return { ok: false, data: [] };
    });
    console.log('Goals response:', res);
    state.goals = res?.data || [];
    console.log('Goals loaded:', state.goals);
    render();
  }

  // Create modal wiring
  els.createBtn?.addEventListener("click", () => {
    openModal(els.createModal);
  });

  document.addEventListener("click", async (e) => {
    const t = e.target;
    if (!(t instanceof HTMLElement)) return;

    if (t.matches("[data-close-modal]")) {
      t.closest(".modal-backdrop")?.classList.remove("show");
    }

    if (t.matches('[data-action="open-edit"]')) {
      const id = Number(t.getAttribute("data-id"));
      const goal = state.goals.find((g) => Number(g.GoalID) === id);
      if (goal) openEdit(goal);
    }
  });

  function openModal(modal) {
    if (!modal) return;
    modal.classList.add("show");
    const form = modal.querySelector("form");
    if (!form) return;
    form.reset();
  }

  function openEdit(goal) {
    const modal = els.editModal;
    if (!modal) return;
    modal.classList.add("show");
    modal.querySelector('[name="goalId"]').value = goal.GoalID;
    modal.querySelector('[name="goalTitle"]').value = goal.GoalTitle || "";
    modal.querySelector('[name="description"]').value = goal.Description || "";
    modal.querySelector('[name="type"]').value = goal.Type;
    modal.querySelector('[name="startTime"]').value = goal.StartTime?.replace(
      " ",
      "T"
    );
    modal.querySelector('[name="endTime"]').value = goal.EndTime
      ? goal.EndTime
      : "— (auto when completed)";
    modal.querySelector('[name="deadline"]').value = goal.Deadline?.replace(
      " ",
      "T"
    );
    modal.querySelector('[name="progress"]').value = goal.Progress;
  }

  // Create submit
  document
    .getElementById("create-goal-form")
    ?.addEventListener("submit", async (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const payload = Object.fromEntries(fd.entries());
      const res = await api.createGoal(payload).catch(() => ({ ok: false }));
      if (res?.ok) {
        els.createModal.classList.remove("show");
        await load();
      } else {
        alert(res?.message || "Failed to create goal");
      }
    });

  // Edit submit
  document
    .getElementById("edit-goal-form")
    ?.addEventListener("submit", async (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const payload = Object.fromEntries(fd.entries());
      const res = await api.updateGoal(payload).catch(() => ({ ok: false }));
      if (res?.ok) {
        els.editModal.classList.remove("show");
        await load();
      } else {
        alert(res?.message || "Failed to update goal");
      }
    });

  // Delete
  document
    .getElementById("delete-goal-btn")
    ?.addEventListener("click", async () => {
      const id = Number(
        document.querySelector('#edit-goal-form [name="goalId"]').value
      );
      if (!id) return;
      if (!confirm("Delete this goal?")) return;
      const res = await api.deleteGoal(id).catch(() => ({ ok: false }));
      if (res?.ok) {
        els.editModal.classList.remove("show");
        await load();
      } else {
        alert(res?.message || "Failed to delete goal");
      }
    });

  // Initial load
  const ready = () => {
    load();
  };
  if (
    document.readyState === "complete" ||
    document.readyState === "interactive"
  )
    ready();
  else document.addEventListener("DOMContentLoaded", ready);
})();
