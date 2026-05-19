<?php $__env->startSection('title', 'Business Settings'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: #163a6f;
        border-bottom: 2px solid #e7f1ff;
        padding-bottom: 8px;
        margin-bottom: 16px;
    }
    .contact-row, .bank-row-item {
        background: #f8faff;
        border: 1px solid #d1dff5;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
    }
    .remove-btn {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #ef4444;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 12px;
        cursor: pointer;
    }
    .add-btn {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 12px;
        cursor: pointer;
        font-weight: 600;
    }
    .qr-preview {
        width: 80px;
        height: 80px;
        object-fit: contain;
        border: 1px solid #d1dff5;
        border-radius: 6px;
        margin-top: 6px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="form-card">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-gear me-2"></i>Business Settings
            </h5>

            <form action="<?php echo e(route('settings.business.update')); ?>"
                  method="POST"
                  enctype="multipart/form-data">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                <!-- Business Info -->
                <div class="section-title">Business Info</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Business Name *</label>
                        <input type="text" name="business_name"
                               class="form-control"
                               value="<?php echo e($setting?->business_name ?? ''); ?>"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="tagline"
                               class="form-control"
                               value="<?php echo e($setting?->tagline); ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Address</label>
                        <input type="text" name="address"
                               class="form-control"
                               value="<?php echo e($setting?->address); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NTN Number</label>
                        <input type="text" name="ntn"
                               class="form-control"
                               value="<?php echo e($setting?->ntn); ?>"
                               placeholder="e.g. 50615561">
                    </div>
                </div>

                <hr>

                <!-- Contacts -->
                <div class="section-title">
                    Contacts
                    <button type="button"
                            class="add-btn float-end"
                            onclick="addContact()">
                        ➕ Add Contact
                    </button>
                </div>

                <div id="contactsContainer">
                    <?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="contact-row" id="contact_<?php echo e($i); ?>">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label">Name *</label>
                                <input type="text"
                                       name="contacts[<?php echo e($i); ?>][name]"
                                       class="form-control"
                                       value="<?php echo e($contact->name); ?>"
                                       placeholder="e.g. Rehan">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Phone *</label>
                                <input type="text"
                                       name="contacts[<?php echo e($i); ?>][phone]"
                                       class="form-control"
                                       value="<?php echo e($contact->phone); ?>"
                                       placeholder="+92 3XX XXXXXXX">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button"
                                        class="remove-btn w-100"
                                        onclick="removeContact('contact_<?php echo e($i); ?>')">
                                    🗑️ Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="contact-row" id="contact_0">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label">Name *</label>
                                <input type="text"
                                       name="contacts[0][name]"
                                       class="form-control"
                                       placeholder="e.g. Rehan">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Phone *</label>
                                <input type="text"
                                       name="contacts[0][phone]"
                                       class="form-control"
                                       placeholder="+92 3XX XXXXXXX">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button"
                                        class="remove-btn w-100"
                                        onclick="removeContact('contact_0')">
                                    🗑️ Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <hr>

                <!-- Banks -->
                <div class="section-title">
                    Bank Details
                    <button type="button"
                            class="add-btn float-end"
                            onclick="addBank()">
                        ➕ Add Bank
                    </button>
                </div>

                <div id="banksContainer">
                    <?php $__empty_1 = true; $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="bank-row-item" id="bank_<?php echo e($bank->id); ?>">
                        <input type="hidden"
                               name="banks[<?php echo e($i); ?>][id]"
                               value="<?php echo e($bank->id); ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Bank Name *</label>
                                <input type="text"
                                       name="banks[<?php echo e($i); ?>][bank_name]"
                                       class="form-control"
                                       value="<?php echo e($bank->bank_name); ?>"
                                       placeholder="e.g. Meezan Bank">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Account Title</label>
                                <input type="text"
                                       name="banks[<?php echo e($i); ?>][account_title]"
                                       class="form-control"
                                       value="<?php echo e($bank->account_title); ?>"
                                       placeholder="STAR COMPUTER">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Account Number</label>
                                <input type="text"
                                       name="banks[<?php echo e($i); ?>][account_number]"
                                       class="form-control"
                                       value="<?php echo e($bank->account_number); ?>"
                                       placeholder="01780104703562">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">IBAN</label>
                                <input type="text"
                                       name="banks[<?php echo e($i); ?>][iban]"
                                       class="form-control"
                                       value="<?php echo e($bank->iban); ?>"
                                       placeholder="PK28MEZN...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">
                                    QR Code
                                    <small class="text-muted">(Optional)</small>
                                </label>
                                <input type="file"
                                       name="banks[<?php echo e($i); ?>][qr_file]"
                                       class="form-control"
                                       accept="image/*">
                                <?php if($bank->qr_code): ?>
                                <img src="<?php echo e(asset('storage/'.$bank->qr_code)); ?>"
                                     class="qr-preview"
                                     alt="QR Code">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button"
                                        class="remove-btn w-100"
                                        onclick="removeBank(<?php echo e($bank->id); ?>, 'bank_<?php echo e($bank->id); ?>')">
                                    🗑️ Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="bank-row-item" id="bank_new_0">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Bank Name *</label>
                                <input type="text"
                                       name="banks[0][bank_name]"
                                       class="form-control"
                                       placeholder="e.g. Meezan Bank">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Account Title</label>
                                <input type="text"
                                       name="banks[0][account_title]"
                                       class="form-control"
                                       placeholder="STAR COMPUTER">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Account Number</label>
                                <input type="text"
                                       name="banks[0][account_number]"
                                       class="form-control"
                                       placeholder="01780104703562">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">IBAN</label>
                                <input type="text"
                                       name="banks[0][iban]"
                                       class="form-control"
                                       placeholder="PK28MEZN...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">QR Code</label>
                                <input type="file"
                                       name="banks[0][qr_file]"
                                       class="form-control"
                                       accept="image/*">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button"
                                        class="remove-btn w-100"
                                        onclick="removeNewBank('bank_new_0')">
                                    🗑️ Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Hidden delete banks -->
                <div id="deleteBanksContainer"></div>

                <hr>

                <!-- Notes & Thank You -->
                <div class="section-title">Other</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Notes</label>
                        <textarea name="notes"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Warranty info..."><?php echo e($setting?->notes); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Thank You Message</label>
                        <textarea name="thank_you_message"
                                  class="form-control"
                                  rows="4"><?php echo e($setting?->thank_you_message); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save Settings
                </button>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let contactCount = <?php echo e($contacts->count()); ?>;
let bankCount    = <?php echo e($banks->count()); ?>;
let newBankCount = 0;

function addContact() {
    const container = document.getElementById('contactsContainer');
    const id = 'contact_new_' + contactCount;
    const html = `
        <div class="contact-row" id="${id}">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <label class="form-label">Name *</label>
                    <input type="text"
                           name="contacts[${contactCount}][name]"
                           class="form-control"
                           placeholder="e.g. Rehan">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Phone *</label>
                    <input type="text"
                           name="contacts[${contactCount}][phone]"
                           class="form-control"
                           placeholder="+92 3XX XXXXXXX">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button"
                            class="remove-btn w-100"
                            onclick="removeContact('${id}')">
                        🗑️ Remove
                    </button>
                </div>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    contactCount++;
}

function removeContact(id) {
    document.getElementById(id)?.remove();
}

function addBank() {
    const container = document.getElementById('banksContainer');
    const idx = bankCount + newBankCount;
    const id  = 'bank_new_' + idx;
    const html = `
        <div class="bank-row-item" id="${id}">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Bank Name *</label>
                    <input type="text"
                           name="banks[${idx}][bank_name]"
                           class="form-control"
                           placeholder="e.g. HBL">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Account Title</label>
                    <input type="text"
                           name="banks[${idx}][account_title]"
                           class="form-control"
                           placeholder="STAR COMPUTER">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Account Number</label>
                    <input type="text"
                           name="banks[${idx}][account_number]"
                           class="form-control"
                           placeholder="Account #">
                </div>
                <div class="col-md-3">
                    <label class="form-label">IBAN</label>
                    <input type="text"
                           name="banks[${idx}][iban]"
                           class="form-control"
                           placeholder="PK...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">QR Code</label>
                    <input type="file"
                           name="banks[${idx}][qr_file]"
                           class="form-control"
                           accept="image/*">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button"
                            class="remove-btn w-100"
                            onclick="removeNewBank('${id}')">
                        🗑️ Remove
                    </button>
                </div>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    newBankCount++;
}

function removeBank(bankId, elemId) {
    document.getElementById(elemId)?.remove();
    const container = document.getElementById('deleteBanksContainer');
    container.insertAdjacentHTML('beforeend',
        `<input type="hidden" name="delete_banks[]" value="${bankId}">`
    );
}

function removeNewBank(id) {
    document.getElementById(id)?.remove();
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\star_computer_accounting\resources\views/settings/business.blade.php ENDPATH**/ ?>