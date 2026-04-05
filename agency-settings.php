<?php $settings = getAgencySettings(); ?>

<div class="card bg-base-100 shadow-sm">
    <div class="card-body p-4">
        <h3 class="card-title text-sm font-bold mb-3"><i class="fas fa-cog mr-2"></i>Agency Settings</h3>
        
        <form id="settingsForm" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Text Fields -->
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Agency Name</span></label>
                    <input type="text" name="agencyName" class="input input-sm input-bordered w-full" value="<?= htmlspecialchars($settings['agencyName'] ?? '') ?>" />
                </div>
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Email</span></label>
                    <input type="email" name="email" class="input input-sm input-bordered w-full" value="<?= htmlspecialchars($settings['email'] ?? '') ?>" />
                </div>
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Address Line 1</span></label>
                    <input type="text" name="address1" class="input input-sm input-bordered w-full" value="<?= htmlspecialchars($settings['address1'] ?? '') ?>" />
                </div>
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Phone</span></label>
                    <input type="text" name="phone" class="input input-sm input-bordered w-full" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" />
                </div>
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Address Line 2</span></label>
                    <input type="text" name="address2" class="input input-sm input-bordered w-full" value="<?= htmlspecialchars($settings['address2'] ?? '') ?>" />
                </div>
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Website</span></label>
                    <input type="text" name="website" class="input input-sm input-bordered w-full" value="<?= htmlspecialchars($settings['website'] ?? '') ?>" />
                </div>
            </div>

            <!-- File Uploads -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Logo</span></label>
                    <input type="file" name="logo" accept="image/*" class="file-input file-input-sm file-input-bordered w-full" onchange="previewImage(this, 'logoPreview')" />
                    <div class="mt-2">
                        <img id="logoPreview" src="<?= !empty($settings['logoPath']) ? '/' . htmlspecialchars($settings['logoPath']) : '' ?>" 
                             class="h-16 object-contain <?= empty($settings['logoPath']) ? 'hidden' : '' ?>" alt="Logo Preview" />
                    </div>
                </div>
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Stamp</span></label>
                    <input type="file" name="stamp" accept="image/*" class="file-input file-input-sm file-input-bordered w-full" onchange="previewImage(this, 'stampPreview')" />
                    <div class="mt-2">
                        <img id="stampPreview" src="<?= !empty($settings['stampPath']) ? '/' . htmlspecialchars($settings['stampPath']) : '' ?>" 
                             class="h-16 object-contain <?= empty($settings['stampPath']) ? 'hidden' : '' ?>" alt="Stamp Preview" />
                    </div>
                </div>
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Signature</span></label>
                    <input type="file" name="signature" accept="image/*" class="file-input file-input-sm file-input-bordered w-full" onchange="previewImage(this, 'signaturePreview')" />
                    <div class="mt-2">
                        <img id="signaturePreview" src="<?= !empty($settings['signaturePath']) ? '/' . htmlspecialchars($settings['signaturePath']) : '' ?>" 
                             class="h-16 object-contain <?= empty($settings['signaturePath']) ? 'hidden' : '' ?>" alt="Signature Preview" />
                    </div>
                </div>
            </div>

            <!-- Terms & Conditions -->
            <div class="mt-4">
                <label class="label py-0"><span class="label-text text-sm">Terms & Conditions</span></label>
                <textarea name="termsAndConditions" class="textarea textarea-bordered w-full h-32 text-sm" placeholder="Enter your terms and conditions..."><?= htmlspecialchars($settings['termsAndConditions'] ?? '') ?></textarea>
            </div>

            <!-- Save Button -->
            <div class="mt-4 text-right">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-save mr-1"></i> Save Settings
                </button>
            </div>
        </form>

        <div id="settingsAlert" class="alert mt-3 hidden"></div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const alertEl = document.getElementById('settingsAlert');
    
    fetch('/api/settings.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alertEl.className = 'alert alert-success mt-3';
            alertEl.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Settings saved successfully!';
            // Update booking form agency fields if they exist
            if (data.settings) {
                const bf_name = document.getElementById('bf_agencyName');
                const bf_email = document.getElementById('bf_agencyEmail');
                const bf_phone = document.getElementById('bf_agencyPhone');
                if (bf_name) bf_name.value = data.settings.agencyName || '';
                if (bf_email) bf_email.value = data.settings.email || '';
                if (bf_phone) bf_phone.value = data.settings.phone || '';
            }
        } else {
            alertEl.className = 'alert alert-error mt-3';
            alertEl.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> ' + (data.error || 'Failed to save settings.');
        }
        alertEl.classList.remove('hidden');
        setTimeout(() => alertEl.classList.add('hidden'), 3000);
    })
    .catch(err => {
        alertEl.className = 'alert alert-error mt-3';
        alertEl.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Network error.';
        alertEl.classList.remove('hidden');
        setTimeout(() => alertEl.classList.add('hidden'), 3000);
    });
});
</script>
