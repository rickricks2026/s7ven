-- Executar no Supabase após as duas migrações de tabelas.
insert into storage.buckets(id,name,public,file_size_limit,allowed_mime_types) values
('s7ven-media','s7ven-media',true,5242880,array['image/png','image/jpeg','image/webp']),
('s7ven-deliveries','s7ven-deliveries',false,26214400,array['application/zip'])
on conflict(id) do nothing;
create policy s7ven_media_upload on storage.objects for insert to authenticated with check(bucket_id='s7ven-media' and public.is_admin());
create policy s7ven_media_admin_read on storage.objects for select to authenticated using(bucket_id='s7ven-media' and public.is_admin());
create policy s7ven_media_delete on storage.objects for delete to authenticated using(bucket_id='s7ven-media' and public.is_admin());
create policy s7ven_delivery_upload on storage.objects for insert to authenticated with check(bucket_id='s7ven-deliveries' and public.is_admin());
create policy s7ven_delivery_delete on storage.objects for delete to authenticated using(bucket_id='s7ven-deliveries' and public.is_admin());
create policy s7ven_delivery_read on storage.objects for select to authenticated using(bucket_id='s7ven-deliveries' and (public.is_admin() or (public.account_enabled() and exists(select 1 from public.product_files f join public.licenses l on l.product_id=f.product_id where f.path=storage.objects.name and l.user_id=auth.uid() and l.status='active' and (l.expires_at is null or l.expires_at>=current_date)))));
