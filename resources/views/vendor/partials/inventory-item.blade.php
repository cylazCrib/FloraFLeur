<div class="inventory-item" 
     data-id="{{ $item->id }}" 
     data-name="{{ $item->name }}"
     data-code="{{ $item->code }}"
     data-quantity="{{ $item->quantity }}"
     data-type="{{ $item->type }}"
     data-update-url="{{ route('vendor.inventory.update', $item->id) }}"
     data-delete-url="{{ route('vendor.inventory.destroy', $item->id) }}">
    
    <div class="inventory-item-details">
        <div class="inventory-item-name">
            @if($item->code)<small>{{ $item->code }} - </small>@endif
            <span>{{ $item->name }}</span>
        </div>
        <div class="inventory-item-stock">
            <span>{{ $item->quantity }} pcs remaining</span>
            @if($item->quantity <= 5)
                <span class="low-stock-alert" style="margin-left: 10px; color: red; font-weight: bold;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Low Stock!
                </span>
            @endif
        </div>
    </div>
    <div class="inventory-item-actions">
        <button class="action-button update-item-btn">Update</button>
        <button class="action-button remove-btn" style="background-color: #E5B2B6; color: #7F343A;">Remove</button>
    </div>
</div>