let rec html2string (ch: in_channel): string = 
    match input_line ch with
    | str -> String.trim(str) ^ (html2string ch)
    | exception End_of_file -> ""

let write (s: string) : unit =
    let ch = open_out "stringifiedhtml.txt" in
    let _ = Printf.fprintf ch "%s\n" s in
    let _ = close_out ch in
    ()

let parse (input: string) : unit =
    let ch = open_in input in
    let stringifiedhtml = html2string ch in 
    write stringifiedhtml 