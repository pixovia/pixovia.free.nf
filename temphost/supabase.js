// supabase.js
import { createClient } from "https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2/+esm";

const SUPABASE_URL = "https://fxcfusffpncorldogyku.supabase.co";
const SUPABASE_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZ4Y2Z1c2ZmcG5jb3JsZG9neWt1Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3Njc0NDEwMzQsImV4cCI6MjA4MzAxNzAzNH0.7lF1aYo4eP2by4wAnSCTpqhElgN6_TdPsItvQtxNGPI";

export const supabase = createClient(SUPABASE_URL, SUPABASE_KEY);

// Optional helper to get all courses
export async function getCourses() {
  const { data, error } = await supabase
    .from("learn")
    .select("std, course")
    .eq("institution", "eduport")
    .order("std", { ascending: true });
  if (error) throw error;
  return data;
}