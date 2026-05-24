# Dynamic Forms Refactor for Dupak Kategori (36+ Special Cases)
Status: ✅ In Progress | Plan Approved

## Steps from Approved Plan:

### 1. **✅ Create TODO.md** 
   - Track progress (current)

### 2. **Edit Controller** `app/Http/Controllers/Dupak/DetilPengajuanController.php`
   - [ ] Add $formConfigs array with ID=3 (perkuliahan) + samples 4,5,6
   - [ ] Update showForm(): load config, pass $formConfig
   - [ ] Remove booleans ($isPerkuliahan etc.) & unused switch

### 3. **Edit View** `resources/views/dupak/pengisian_detil_pengajuan/generic_form.blade.php`
   - [ ] Replace @if($isPerkuliahan) with @foreach($formConfig['specialFields'])
   - [ ] Add generic field loop with data attrs
   - [ ] Volume input: dynamic readonly + data-volume-calc
   - [ ] Update JS: generic calculateVolume()

### 4. **Test Existing Flow**
   - [ ] ID=3 (perkuliahan): unchanged behavior
   - [ ] Non-special IDs: no breakage

### 5. **Enhancements**
   - [ ] Add config/dupak.php (future)
   - [ ] Sample configs for 36+ via seeder

### 6. **Completion**
   - [ ] Update this TODO with ✅
   - [ ] attempt_completion

Next: Edit Controller → confirm → View → test.

