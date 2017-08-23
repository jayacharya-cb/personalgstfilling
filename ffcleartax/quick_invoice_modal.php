 <div class="modal  fade in" id="shipping-address-modal" tabindex="-1" role="shipping-address-modal" aria-hidden="true">
							<div class="modal-dialog modal-sm">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
										<h4 class="modal-title">Edit Shipping Address</h4>
									</div>
									<form role="form" name="shipping-address-form" id="shipping-address-form" type="POST">
									<div class="modal-body modal-sm">								
											<div class="form-body">
												<div class="form-group">
													<label>Address</label>
													<textarea placeholder="Address"  name="edit_shipping_address" id="edit_shipping_address" class="form-control">
													</textarea>
												</div>
											</div>
											<div class="form-body">
												<div class="form-group">
													<label>Pincode</label>
													<input class="form-control" name="edit_shipping_pincode" id="edit_shipping_pincode" placeholder="Pincode" type="text"> 
												</div>
											</div>
											<div class="form-body">
												<div class="form-group">
													<label>City</label>
													<input class="form-control" name="edit_shipping_city" id="edit_shipping_city" placeholder="City" type="text"> 
												</div>
											</div>
											<div class="form-body">
												<div class="form-group">
													<label>State</label>
													<select class="form-control" name="edit_shipping_state" id="edit_shipping_state" placeholder="State" type="text">
														<option value="">Select State</option>
														<?php 
															if($states)
															{
																foreach($states as $state)
																{
																	
																	?>
																	<option  value="<?php echo $state['slug'] ?>">
																	<?php echo $state['name']; ?>
																	</option>
																	<?php 
																}
															}
														?>
													</select>
												</div>
											</div>
										
									</div>
									<div class="modal-footer">
										<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
										<button type="button" class="btn green" id="ssa-btn">Save changes</button>
									</div>
									</form>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
<div class="modal  fade in" id="billing-address-modal" tabindex="-1" role="billing-address-modal" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Edit Billing Address</h4>
			</div>
			<form role="form" id="billing-address-form" name="billing-address-form" type="POST">
			<div class="modal-body modal-sm">
				
					<div class="form-body">
						<div class="form-group">
							<label>Address</label>
							<textarea placeholder="Address" name="edit_billing_address" id="edit_billing_address" class="form-control">
							</textarea>
						</div>
					</div>
					<div class="form-body">
						<div class="form-group">
							<label>Pincode</label>
							<input class="form-control" name="edit_billing_pincode" id="edit_billing_pincode" placeholder="Pincode" type="text"> 
						</div>
					</div>
					<div class="form-body">
						<div class="form-group">
							<label>City</label>
							<input class="form-control" name="edit_billing_city" id="edit_billing_city" placeholder="City" type="text"> 
						</div>
					</div>
					<div class="form-body">
						<div class="form-group">
							<label>State</label>
							<select class="form-control" name="edit_billing_state" id="edit_billing_state" placeholder="State" type="text">
								<option value="" >Select State</option>
								<?php 
									if($states)
									{
										foreach($states as $state)
										{
											?>
											<option  value="<?php echo $state['slug'] ?>">
											<?php echo $state['name']; ?>
											</option>
											<?php 
										}
									}
								?>
							</select>
						</div>
					</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
				<button type="button" class="btn green" id="sba-btn">Save changes</button>
			</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
