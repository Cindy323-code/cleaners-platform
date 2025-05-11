<!-- Generic Filter Form Component -->
<div class="filter-container">
    <h4>Filter Options</h4>
    <form id="filter-form" method="GET" class="filter-form">
        <?php if (isset($context['keyword'])): ?>
            <input type="hidden" name="keyword" value="<?= htmlspecialchars($context['keyword'] ?? '') ?>">
        <?php endif; ?>
        
        <!-- Common filters -->
        <div class="filter-group">
            <label for="price_min">Min Price:</label>
            <input type="number" id="price_min" name="price_min" min="0" step="0.01" 
                   value="<?= htmlspecialchars($context['filters']['price_min'] ?? '') ?>">
        </div>
        
        <div class="filter-group">
            <label for="price_max">Max Price:</label>
            <input type="number" id="price_max" name="price_max" min="0" step="0.01"
                   value="<?= htmlspecialchars($context['filters']['price_max'] ?? '') ?>">
        </div>
        
        <!-- Service Type filter -->
        <?php if (isset($context['show_service_type']) && $context['show_service_type']): ?>
        <div class="filter-group">
            <label for="type">Service Type:</label>
            <select id="type" name="type">
                <option value="">All Types</option>
                <?php foreach ($context['service_types'] ?? [] as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>" 
                            <?= (isset($context['filters']['type']) && $context['filters']['type'] === $type) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($type) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        
        <!-- Date filters -->
        <?php if (isset($context['show_date_filter']) && $context['show_date_filter']): ?>
        <div class="filter-group">
            <label for="date_from">From Date:</label>
            <input type="date" id="date_from" name="date_from" 
                   value="<?= htmlspecialchars($context['filters']['date_from'] ?? '') ?>">
        </div>
        
        <div class="filter-group">
            <label for="date_to">To Date:</label>
            <input type="date" id="date_to" name="date_to"
                   value="<?= htmlspecialchars($context['filters']['date_to'] ?? '') ?>">
        </div>
        <?php endif; ?>
        
        <!-- Status filter -->
        <?php if (isset($context['show_status_filter']) && $context['show_status_filter']): ?>
        <div class="filter-group">
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="">All Statuses</option>
                <option value="confirmed" <?= (isset($context['filters']['status']) && $context['filters']['status'] === 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                <option value="completed" <?= (isset($context['filters']['status']) && $context['filters']['status'] === 'completed') ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled" <?= (isset($context['filters']['status']) && $context['filters']['status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        <?php endif; ?>
        
        <!-- Person filter (cleaner or homeowner) -->
        <?php if (isset($context['show_person_filter']) && $context['show_person_filter']): ?>
        <div class="filter-group">
            <label for="person"><?= htmlspecialchars($context['person_label'] ?? 'Person') ?>:</label>
            <input type="text" id="person" name="<?= htmlspecialchars($context['person_field'] ?? 'person') ?>" 
                   value="<?= htmlspecialchars($context['filters'][$context['person_field'] ?? 'person'] ?? '') ?>" 
                   placeholder="Enter name...">
        </div>
        <?php endif; ?>
        
        <!-- Availability filter -->
        <?php if (isset($context['show_availability_filter']) && $context['show_availability_filter']): ?>
        <div class="filter-group">
            <label for="availability">Availability:</label>
            <input type="text" id="availability" name="availability" 
                   value="<?= htmlspecialchars($context['filters']['availability'] ?? '') ?>" 
                   placeholder="e.g. weekends, mornings">
        </div>
        <?php endif; ?>
        
        <!-- Sort options -->
        <div class="filter-group">
            <label for="sort_by">Sort By:</label>
            <select id="sort_by" name="sort_by">
                <option value="">Default</option>
                <option value="price" <?= (isset($context['filters']['sort_by']) && $context['filters']['sort_by'] === 'price') ? 'selected' : '' ?>>Price</option>
                <option value="name" <?= (isset($context['filters']['sort_by']) && $context['filters']['sort_by'] === 'name') ? 'selected' : '' ?>>Name</option>
                <?php if (isset($context['show_date_sort']) && $context['show_date_sort']): ?>
                <option value="date" <?= (isset($context['filters']['sort_by']) && $context['filters']['sort_by'] === 'date') ? 'selected' : '' ?>>Date</option>
                <?php endif; ?>
                <?php if (isset($context['show_service_type']) && $context['show_service_type']): ?>
                <option value="type" <?= (isset($context['filters']['sort_by']) && $context['filters']['sort_by'] === 'type') ? 'selected' : '' ?>>Type</option>
                <?php endif; ?>
                <?php if (isset($context['show_status_filter']) && $context['show_status_filter']): ?>
                <option value="status" <?= (isset($context['filters']['sort_by']) && $context['filters']['sort_by'] === 'status') ? 'selected' : '' ?>>Status</option>
                <?php endif; ?>
                <?php if (isset($context['show_person_filter']) && $context['show_person_filter']): ?>
                <option value="<?= htmlspecialchars($context['person_sort'] ?? 'person') ?>" 
                        <?= (isset($context['filters']['sort_by']) && $context['filters']['sort_by'] === ($context['person_sort'] ?? 'person')) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($context['person_label'] ?? 'Person') ?>
                </option>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="sort_dir">Sort Direction:</label>
            <select id="sort_dir" name="sort_dir">
                <option value="asc" <?= (isset($context['filters']['sort_dir']) && $context['filters']['sort_dir'] === 'asc') ? 'selected' : '' ?>>Ascending</option>
                <option value="desc" <?= (isset($context['filters']['sort_dir']) && $context['filters']['sort_dir'] === 'desc') ? 'selected' : '' ?>>Descending</option>
            </select>
        </div>
        
        <div class="filter-actions">
            <button type="submit" class="btn-primary">Apply Filters</button>
            <a href="<?= htmlspecialchars($context['reset_url'] ?? '?') ?>" class="btn-secondary">Reset</a>
        </div>
    </form>
</div>

<style>
.filter-container {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.filter-group {
    flex: 0 0 calc(33.33% - 10px);
    margin-bottom: 10px;
}

.filter-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.filter-group input,
.filter-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.filter-actions {
    flex: 0 0 100%;
    margin-top: 10px;
    display: flex;
    gap: 10px;
}

.btn-primary,
.btn-secondary {
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    border: none;
    display: inline-block;
}

@media (max-width: 768px) {
    .filter-group {
        flex: 0 0 100%;
    }
}
</style> 